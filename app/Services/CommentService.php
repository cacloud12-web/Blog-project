<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentService
{
    public function __construct(
        private readonly ContentSanitizer $sanitizer,
    ) {}

    public function createComment(Post $post, User $user, string $comment): Comment
    {
        return $post->comments()->create([
            'user_id' => $user->id,
            'comment' => $this->sanitizer->sanitize($comment),
            'is_approved' => $user->isAdmin(),
        ]);
    }

    public function approve(Comment $comment): Comment
    {
        $comment->update(['is_approved' => true]);

        return $comment->refresh();
    }

    public function reject(Comment $comment): void
    {
        $comment->delete();
    }
}
