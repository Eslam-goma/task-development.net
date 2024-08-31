<?php
namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name',
        ]);

        return Tag::create($request->only('name'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|unique:tags,name,' . $tag->id,
        ]);

        $tag->update($request->only('name'));

        return $tag;
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return response()->noContent();
    }
}
