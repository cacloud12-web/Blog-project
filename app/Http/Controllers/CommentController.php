<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
    ) {}

    public function store(StoreCommentRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('view', $post);

        $comment = $this->commentService->createComment(
            $post,
            $request->user(),
            $request->validated('comment'),
        );

        $message = $comment->is_approved
            ? 'Comment added successfully!'
            : 'Comment submitted and is pending approval.';

        return redirect()
            ->route('posts.show', $post)
            ->with('success', $message);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $this->authorize('approve', $comment);

        $this->commentService->approve($comment);

        return back()->with('success', 'Comment approved.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $post = $comment->post;
        $this->commentService->reject($comment);

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Comment removed.');
    }
}
