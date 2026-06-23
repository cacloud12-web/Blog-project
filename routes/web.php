<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('posts', PostController::class)
    ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], 'auth');

Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])
    ->middleware(['auth', 'throttle:comments'])
    ->name('comments.store');

Route::middleware('auth')->group(function () {
    Route::post('/comments/{comment}/approve', [CommentController::class, 'approve'])
        ->name('comments.approve');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::delete('/tags/{tag}', [CategoryController::class, 'destroyTag'])->name('tags.destroy');
});

Route::get('/profile', [ProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profile.edit');

Route::patch('/profile', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->middleware('auth')
    ->name('profile.destroy');

require __DIR__.'/auth.php';
