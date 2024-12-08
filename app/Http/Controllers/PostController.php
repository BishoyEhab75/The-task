<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function view()
    {
        $posts = Post::where('user_id', Auth::id())
            ->orderByDesc('pinned')
            ->get();

        return PostResource::collection($posts);
    }

    public function show($id)
    {
        $post = Post::where('user_id', Auth::id())->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found or unauthorized'], 404);
        }

        return response()->json($post);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image',
            'pinned' => 'required|boolean',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Handle file upload for cover image
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('cover_images');
        }

        // Create the post
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'cover_image' => $path ?? null,
            'pinned' => $request->pinned,
            'user_id' => Auth::id(),
        ]);
        $post->user->increment('posts_count');
        $post->save();

        // Attach tags if provided
        if ($request->tags) {
            $post->tags()->attach($request->tags);
        }

        return new PostResource($post);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'nullable|image',
            'pinned' => 'nullable|boolean',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $post = Post::where('user_id', Auth::id())->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found or unauthorized'], 404);
        }

        // Handle file upload if cover image is updated
        if ($request->hasFile('cover_image')) {
            // If the post already has a cover image, delete it
            if ($post->cover_image) {
                Storage::delete($post->cover_image);
            }
            $path = $request->file('cover_image')->store('cover_images');
            $post->cover_image = $path;
        }

        // Update post
        $post->update($request->only(['title', 'body', 'pinned']));

        // Sync tags if provided
        if ($request->tags) {
            $post->tags()->sync($request->tags);
        }

        return response()->json($post);
    }

    public function delete($id)
    {
        $post = Post::where('user_id', Auth::id())->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found or unauthorized'], 404);
        }
        $post->user->decrement('posts_count');

        $post->delete();

        return response()->json(['message' => 'Post soft deleted successfully']);
    }

    public function trashed()
    {
        $posts = Post::onlyTrashed()->where('user_id', Auth::id())->get();

        return response()->json($posts);
    }

    // // Restore a soft-deleted post
    public function restore($id)
    {
        $post = Post::onlyTrashed()->where('user_id', Auth::id())->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found or unauthorized'], 404);
        }
        
        $post->user->increment('posts_count');
        $post->restore();

        return response()->json(['message' => 'Post restored successfully']);
    }
}