<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_comment_on_published_post(): void
    {
        $user = User::factory()->reader()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($user)->post(route('comments.store', $post), [
            'comment' => 'Great article!',
        ]);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => 'Great article!',
            'is_approved' => false,
        ]);
    }

    public function test_admin_comments_are_auto_approved(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create();

        $this->actingAs($admin)->post(route('comments.store', $post), [
            'comment' => 'Admin comment',
        ]);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'comment' => 'Admin comment',
            'is_approved' => true,
        ]);
    }

    public function test_comment_content_is_sanitized(): void
    {
        $user = User::factory()->reader()->create();
        $post = Post::factory()->published()->create();

        $this->actingAs($user)->post(route('comments.store', $post), [
            'comment' => '<b>Bold</b><script>evil()</script> text',
        ]);

        $comment = Comment::first();
        $this->assertNotNull($comment);
        $this->assertStringNotContainsString('<script>', $comment->comment);
        $this->assertStringNotContainsString('<b>', $comment->comment);
    }

    public function test_admin_can_approve_pending_comment(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->published()->create();
        $comment = Comment::create([
            'user_id' => User::factory()->create()->id,
            'post_id' => $post->id,
            'comment' => 'Pending comment',
            'is_approved' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('comments.approve', $comment));

        $response->assertRedirect();
        $this->assertTrue($comment->fresh()->is_approved);
    }

    public function test_non_admin_cannot_approve_comments(): void
    {
        $reader = User::factory()->reader()->create();
        $comment = Comment::create([
            'user_id' => $reader->id,
            'post_id' => Post::factory()->published()->create()->id,
            'comment' => 'Pending',
            'is_approved' => false,
        ]);

        $this->actingAs($reader)->post(route('comments.approve', $comment))->assertForbidden();
    }

    public function test_comment_requires_content(): void
    {
        $user = User::factory()->reader()->create();
        $post = Post::factory()->published()->create();

        $response = $this->actingAs($user)->post(route('comments.store', $post), [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors('comment');
    }
}
