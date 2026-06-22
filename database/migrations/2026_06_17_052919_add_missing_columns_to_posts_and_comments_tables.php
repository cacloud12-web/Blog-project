<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }

            if (! Schema::hasColumn('posts', 'title')) {
                $table->string('title')->after('user_id');
            }

            if (! Schema::hasColumn('posts', 'content')) {
                $table->text('content')->after('title');
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            if (! Schema::hasColumn('comments', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }

            if (! Schema::hasColumn('comments', 'post_id')) {
                $table->foreignId('post_id')->after('user_id')->constrained()->onDelete('cascade');
            }

            if (! Schema::hasColumn('comments', 'comment')) {
                $table->text('comment')->after('post_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'user_id')) {
                $table->dropForeign(['user_id']);
            }

            if (Schema::hasColumn('comments', 'post_id')) {
                $table->dropForeign(['post_id']);
            }

            $columns = array_filter(
                ['user_id', 'post_id', 'comment'],
                fn (string $column) => Schema::hasColumn('comments', $column)
            );

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'user_id')) {
                $table->dropForeign(['user_id']);
            }

            $columns = array_filter(
                ['user_id', 'title', 'content'],
                fn (string $column) => Schema::hasColumn('posts', $column)
            );

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
