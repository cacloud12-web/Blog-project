<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Redirect the homepage to the blog posts listing.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('posts.index');
    }
}
