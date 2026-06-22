<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Blog') - Laravel Blog</title>
    <style>
        /* Basic page styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        a {
            color: #2563eb;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Navbar */
        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
        }

        .navbar-inner {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .navbar-brand {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .navbar-links a {
            margin-left: 15px;
            font-size: 14px;
        }

        .navbar-user {
            font-size: 14px;
            color: #555;
        }

        /* Page heading section */
        .page-heading {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            padding: 30px 20px;
            text-align: center;
        }

        .page-heading h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .page-heading p {
            color: #666;
            font-size: 15px;
        }

        /* Main content area */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        /* Flash messages */
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Blog cards on index page */
        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .post-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
        }

        .post-card-meta {
            font-size: 13px;
            color: #777;
            margin-bottom: 8px;
        }

        .post-card h2 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #222;
        }

        .post-card p {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            text-decoration: none;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            color: #fff;
        }

        .btn-secondary {
            background-color: #fff;
            color: #333;
            border-color: #ccc;
        }

        .btn-secondary:hover {
            background-color: #f0f0f0;
            color: #333;
        }

        .btn-danger {
            background-color: #fff;
            color: #dc2626;
            border-color: #dc2626;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            color: #fff;
        }

        /* Forms */
        .form-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 25px;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: inherit;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .form-error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 4px;
        }

        .form-actions {
            margin-top: 10px;
        }

        .form-actions .btn {
            margin-right: 10px;
        }

        /* Single post page */
        .post-detail {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .post-detail-meta {
            font-size: 13px;
            color: #777;
            margin-bottom: 10px;
        }

        .post-detail h1 {
            font-size: 26px;
            margin-bottom: 15px;
            color: #222;
        }

        .post-detail-content {
            font-size: 15px;
            color: #444;
            white-space: pre-wrap;
            margin-bottom: 20px;
        }

        .post-actions {
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .post-actions .btn {
            margin-right: 10px;
        }

        .inline-form {
            display: inline;
        }

        /* Comments section */
        .comments-section h2 {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .comment-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .comment-meta {
            font-size: 13px;
            color: #777;
            margin-bottom: 6px;
        }

        .comment-text {
            font-size: 14px;
            color: #444;
            white-space: pre-wrap;
        }

        .comment-form-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
        }

        .comment-form-box h3 {
            font-size: 16px;
            margin-bottom: 12px;
        }

        .login-prompt {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .empty-state p {
            color: #777;
            margin: 10px 0 20px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .navbar-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar-links a {
                margin-left: 0;
                margin-right: 15px;
            }

            .post-detail h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    {{-- Simple navigation bar --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('posts.index') }}" class="navbar-brand">Laravel Blog</a>

            <div class="navbar-links">
                <a href="{{ route('posts.index') }}">Home</a>

                @auth
                    <a href="{{ route('posts.create') }}">New Post</a>
                    <span class="navbar-user">Hello, {{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline-form" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="margin-left:10px;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Optional page heading (used on index) --}}
    @hasSection('heading')
        @yield('heading')
    @endif

    <div class="container">

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

</body>
</html>
