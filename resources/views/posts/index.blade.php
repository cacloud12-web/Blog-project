@extends('layouts.blog')

@section('title', 'All Posts')

{{-- Blog heading section --}}
@section('heading')
    <section class="page-heading">
        <h1>My Blog</h1>
        <p>Read posts from our community. Login to write and comment.</p>
    </section>
@endsection

@section('content')

    @if($posts->isEmpty())
        <div class="empty-state">
            <h2>No posts yet</h2>
            <p>Be the first one to write a post.</p>
            @auth
                <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login to Create Post</a>
            @endauth
        </div>
    @else
        <div class="posts-grid">
            @foreach($posts as $post)
                <article class="post-card">
                    {{-- Author and date --}}
                    <div class="post-card-meta">
                        By {{ $post->user->name }}
                        &middot;
                        {{ $post->created_at->format('M d, Y') }}
                    </div>

                    {{-- Post title --}}
                    <h2>{{ $post->title }}</h2>

                    {{-- Short preview of content --}}
                    <p>{{ Str::limit($post->content, 120) }}</p>

                    {{-- Read More button --}}
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">Read More</a>
                </article>
            @endforeach
        </div>
    @endif

@endsection
