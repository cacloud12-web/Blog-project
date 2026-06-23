<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_published_posts_index(): void
    {
        Post::factory()->count(2)->published()->create();

        $response = $this->get(route('posts.index'));

        $response->assertOk();
        $response->assertSee('My Blog');
    }

    public function test_draft_posts_are_hidden_from_public_index(): void
    {
        $draft = Post::factory()->draft()->create(['title' => 'Secret Draft Title']);

        $response = $this->get(route('posts.index'));

        $response->assertOk();
        $response->assertDontSee('Secret Draft Title');
    }

    public function test_search_finds_posts_by_title_on_sqlite(): void
    {
        Post::factory()->published()->create(['title' => 'Laravel Testing Guide']);
        Post::factory()->published()->create(['title' => 'Cooking Recipes']);

        $response = $this->get(route('posts.index', ['search' => 'laravel']));

        $response->assertOk();
        $response->assertSee('Laravel Testing Guide');
        $response->assertDontSee('Cooking Recipes');
    }

    public function test_category_filter_works(): void
    {
        $category = Category::factory()->create(['name' => 'Tech']);
        $other = Category::factory()->create(['name' => 'Food']);

        Post::factory()->published()->forCategory($category)->create(['title' => 'PHP Post']);
        Post::factory()->published()->forCategory($other)->create(['title' => 'Pizza Post']);

        $response = $this->get(route('posts.index', ['category_id' => $category->id]));

        $response->assertOk();
        $response->assertSee('PHP Post');
        $response->assertDontSee('Pizza Post');
    }

    public function test_tag_filter_works(): void
    {
        $post = Post::factory()->published()->create(['title' => 'Tagged Post']);
        $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $post->tags()->attach($tag);

        Post::factory()->published()->create(['title' => 'Untagged Post']);

        $response = $this->get(route('posts.index', ['tag' => 'laravel']));

        $response->assertOk();
        $response->assertSee('Tagged Post');
        $response->assertDontSee('Untagged Post');
    }

    public function test_view_count_increments_when_viewing_published_post(): void
    {
        $post = Post::factory()->published()->create(['view_count' => 5]);

        $this->get(route('posts.show', $post))->assertOk();

        $this->assertSame(6, $post->fresh()->view_count);
    }

    public function test_author_can_create_post(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'My New Post',
            'content' => 'This is valid post content.',
            'status' => 'published',
            'category_id' => $category->id,
            'tags' => 'Laravel, PHP',
        ]);

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'My New Post',
            'user_id' => $author->id,
            'status' => 'published',
        ]);
        $this->assertDatabaseHas('tags', ['slug' => 'laravel']);
    }

    public function test_reader_cannot_create_post(): void
    {
        $reader = User::factory()->reader()->create();

        $response = $this->actingAs($reader)->post(route('posts.store'), [
            'title' => 'Blocked Post',
            'content' => 'This should not be saved.',
            'status' => 'published',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', ['title' => 'Blocked Post']);
    }

    public function test_author_can_edit_own_post_but_not_others(): void
    {
        $author = User::factory()->author()->create();
        $other = User::factory()->author()->create();
        $ownPost = Post::factory()->for($author)->published()->create();
        $otherPost = Post::factory()->for($other)->published()->create();

        $this->actingAs($author)->get(route('posts.edit', $ownPost))->assertOk();
        $this->actingAs($author)->get(route('posts.edit', $otherPost))->assertForbidden();
    }

    public function test_admin_can_edit_any_post(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->for($author)->published()->create();

        $this->actingAs($admin)->get(route('posts.edit', $post))->assertOk();
    }

    public function test_soft_deleted_post_is_not_accessible(): void
    {
        $post = Post::factory()->published()->create();
        $slug = $post->slug;
        $post->delete();

        $this->get(route('posts.show', $slug))->assertNotFound();
    }

    public function test_post_content_is_sanitized_on_create(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'XSS Test Post',
            'content' => '<script>alert("xss")</script>Safe content here.',
            'status' => 'published',
        ]);

        $post = Post::where('title', 'XSS Test Post')->first();
        $this->assertNotNull($post);
        $this->assertStringNotContainsString('<script>', $post->content);
        $this->assertStringContainsString('Safe content here.', $post->content);
    }

    public function test_featured_image_upload_is_validated(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Image Post',
            'content' => 'Content with invalid image.',
            'status' => 'published',
            'featured_image' => UploadedFile::fake()->create('doc.pdf', 100),
        ]);

        $response->assertSessionHasErrors('featured_image');
    }

    public function test_valid_featured_image_is_stored(): void
    {
        Storage::fake('public');
        $author = User::factory()->author()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Image Post',
            'content' => 'Content with valid image.',
            'status' => 'published',
            'featured_image' => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $post = Post::where('title', 'Image Post')->first();
        $this->assertNotNull($post->featured_image);
        Storage::disk('public')->assertExists($post->featured_image);
    }

    public function test_post_listing_uses_cache_for_unfiltered_requests(): void
    {
        Cache::flush();
        Post::factory()->count(3)->published()->create();

        $this->get(route('posts.index'))->assertOk();

        $this->assertTrue(
            Cache::has('posts.1.published.list.p1.9') || Cache::has('posts.2.published.list.p1.9'),
            'Expected cached post listing key to exist.',
        );
    }

    public function test_route_model_binding_uses_slug(): void
    {
        $post = Post::factory()->published()->create(['slug' => 'my-custom-slug']);

        $this->get(route('posts.show', 'my-custom-slug'))
            ->assertOk()
            ->assertSee($post->title);
    }
}
