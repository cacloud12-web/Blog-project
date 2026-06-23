<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->isAdmin();
    }
}
