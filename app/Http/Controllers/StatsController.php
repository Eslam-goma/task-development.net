<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index()
    {
        return Cache::remember('stats', 60, function () {
            return [
                'total_users' => User::count(),
                'total_posts' => Post::count(),
                'users_with_no_posts' => User::doesntHave('posts')->count(),
            ];
        });
    }
}
