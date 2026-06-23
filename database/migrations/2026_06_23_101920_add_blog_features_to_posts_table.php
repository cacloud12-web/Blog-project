<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->string('featured_image')->nullable()->after('content');
            $table->unsignedBigInteger('view_count')->default(0)->after('featured_image');
        });

        DB::table('posts')
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->orderBy('id')
            ->get()
            ->each(function ($post) {
                $baseSlug = Str::slug($post->title ?: 'post-'.$post->id);
                $slug = $baseSlug;
                $counter = 1;

                while (DB::table('posts')->where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                    $slug = $baseSlug.'-'.$counter;
                    $counter++;
                }

                DB::table('posts')->where('id', $post->id)->update(['slug' => $slug]);
            });

        Schema::table('posts', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'featured_image', 'view_count']);
        });
    }
};
