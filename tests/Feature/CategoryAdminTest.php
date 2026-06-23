<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_category_management(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.categories.index'))->assertOk();
    }

    public function test_non_admin_cannot_access_category_management(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)->get(route('admin.categories.index'))->assertForbidden();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Science',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Science', 'slug' => 'science']);
    }

    public function test_duplicate_category_names_get_unique_slugs(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Tech', 'slug' => 'tech']);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Tech',
        ]);

        $this->assertDatabaseHas('categories', ['slug' => 'tech-1']);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))->assertRedirect();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
