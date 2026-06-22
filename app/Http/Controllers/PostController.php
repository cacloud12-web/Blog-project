<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of all blog posts.
     */
    public function index()
    {
        $posts = Post::with('user')->withCount('comments')->latest()->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Save a new post to the database.
     */
    public function store(StorePostRequest $request)
    {
        Post::create([
            'user_id' => auth()->id(),
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
        ]);

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display a single post with its comments.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'comments.user']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update an existing post in the database.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
        ]);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Delete a post from the database.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}
