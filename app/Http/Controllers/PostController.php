<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        return Auth::user()->posts()->with('tags')->orderBy('pinned', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image',
            'pinned' => 'required|boolean',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['user_id'] = Auth::id();
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('cover_images');
        }

        $post = Post::create($validated);
        $post->tags()->sync($request->tags);

        return response()->json($post->load('tags'), 201);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return $post->load('tags');
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
            'cover_image' => 'sometimes|image',
            'pinned' => 'sometimes|required|boolean',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($post->cover_image) {
                Storage::delete($post->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('cover_images');
        }

        $post->update($validated);
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        return $post->load('tags');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->noContent();
    }

    public function trashed()
    {
        return Auth::user()->posts()->onlyTrashed()->get();
    }

    public function restore($id)
    {
        $post = Auth::user()->posts()->onlyTrashed()->where('id', $id)->firstOrFail();
        $post->restore();

        return response()->json($post);
    }
}
