<?php

use App\Http\Controllers\CommentController; //store comments 
use App\Http\Controllers\HomeController; //display home page
use App\Http\Controllers\PostController; //display blog posts
use App\Http\Controllers\ProfileController; //display profile page
use Illuminate\Support\Facades\Route; //define routes

Route::get('/', [HomeController::class, 'index'])->name('home'); //display home page

Route::resource('posts', PostController::class) //CRUD operation for blog posts
    ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], 'auth'); //middleware for authentication

Route::post('/posts/{post}/comments', [CommentController::class, 'store']) //store comments 
    ->middleware('auth')//middleware for authentication
    ->name('comments.store');// name of the route 

Route::get('/profile', [ProfileController::class, 'edit'])//display profile page
    ->middleware('auth')//midlleware for authentication
    ->name('profile.edit');// name of the route 

Route::patch('/profile', [ProfileController::class, 'update']) //update profile information
    ->middleware('auth')//middleware for authentication
    ->name('profile.update');//name of the route 

Route::delete('/profile', [ProfileController::class, 'destroy'])//delete profile 
    ->middleware('auth')//middleware for authentication
    ->name('profile.destroy');//name of the route 

require __DIR__.'/auth.php';//load auth route
