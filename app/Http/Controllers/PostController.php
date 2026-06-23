<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
    ) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $categoryId = $request->integer('category_id') ?: null;
        $tagSlug = $request->string('tag')->trim()->toString();
        $perPage = 9;

        $categories = Category::query()->orderBy('name')->get();

        $posts = $this->postService->listPublishedPosts(
            $search,
            $categoryId,
            $tagSlug,
            $perPage,
            $request->integer('page', 1),
        );

        return view('posts.index', compact('posts', 'categories', 'search', 'categoryId', 'tagSlug'));
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        $categories = Category::query()->orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $this->postService->createPost($request->validated());

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post created successfully!');
    }

    public function show(Post $post): View
    {
        $this->authorize('view', $post);

        if ($post->status === 'published') {
            $post = $this->postService->incrementViewCount($post);
        }

        $post->load(['user', 'category', 'tags']);

        if (auth()->user()?->isAdmin()) {
            $post->load(['comments.user']);
        } else {
            $post->load(['approvedComments.user']);
            $post->setRelation('comments', $post->approvedComments);
        }

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $post->load('tags');
        $categories = Category::query()->orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->postService->updatePost($post, $request->validated());

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $this->postService->deletePost($post);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}
