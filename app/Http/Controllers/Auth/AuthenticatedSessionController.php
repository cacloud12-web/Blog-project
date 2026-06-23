<?php

namespace App\Http\Controllers\Auth; // define namespace

use App\Http\Controllers\Controller; // base controller
use App\Http\Requests\Auth\LoginRequest; // login request
use Illuminate\Http\RedirectResponse; // redirect repsonse
use Illuminate\Http\Request; // handle request data
use Illuminate\Support\Facades\Auth; // check login logout authentication
use Illuminate\View\View; // display view

class AuthenticatedSessionController extends Controller // handle login and logout
{
    /**
     * Display the login view.
     */
    public function create(): View// display login view
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse// handle login request
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('posts.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
