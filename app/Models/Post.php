<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // Fields that can be mass-assigned when creating or updating
    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];

    // A post belongs to one user (author)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A post can have many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
