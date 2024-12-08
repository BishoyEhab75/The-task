<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function stats()
    {
        // Number of all users
        $totalUsers = User::count();

        // Number of all posts
        $totalPosts = Post::count();

        // Number of users with 0 posts
        $usersWithNoPosts = User::where('posts_count', 0)->count();

        return response()->json([
            'total_users' => $totalUsers,
            'total_posts' => $totalPosts,
            'users_with_no_posts' => $usersWithNoPosts,
        ]);
    }
}
