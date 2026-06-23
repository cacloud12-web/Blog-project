<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::Admin,
        ]);

        User::factory()->create([
            'name' => 'Author User',
            'email' => 'author@example.com',
            'role' => UserRole::Author,
        ]);

        User::factory()->create([
            'name' => 'Reader User',
            'email' => 'reader@example.com',
            'role' => UserRole::Reader,
        ]);

        $categories = ['Technology', 'Lifestyle', 'Travel'];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }

        $tags = ['Laravel', 'PHP', 'Tutorial', 'News'];

        foreach ($tags as $name) {
            Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }
}
