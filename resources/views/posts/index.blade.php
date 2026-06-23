@extends('layouts.blog')

@section('title', 'All Posts')

@section('heading')
    <section class="page-heading">
        <h1>My Blog</h1>
        <p>Read posts from our community. Login to write and comment.</p>
    </section>
@endsection

@section('content')

    <form action="{{ route('posts.index') }}" method="GET" class="search-form">
        <div class="search-row">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search posts..."
                value="{{ $search }}"
            >
            <select name="category_id" class="form-control">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected($categoryId == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    @if($posts->isEmpty())
        <div class="empty-state">
            <h2>No posts yet</h2>
            <p>Be the first one to write a post.</p>
            @auth
                @if(auth()->user()->canManagePosts())
                    <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login to Create Post</a>
            @endauth
        </div>
    @else
        <div class="posts-grid">
            @foreach($posts as $post)
                <article class="post-card">
                    @if($post->featured_image)
                        <img
                            src="{{ Storage::url($post->featured_image) }}"
                            alt="{{ $post->title }}"
                            class="post-card-image"
                        >
                    @endif

                    <div class="post-card-meta">
                        By {{ $post->user->name }}
                        &middot;
                        {{ $post->created_at->format('M d, Y') }}
                        @if($post->category)
                            &middot; {{ $post->category->name }}
                        @endif
                        &middot; {{ $post->view_count }} views
                    </div>

                    <h2>{{ $post->title }}</h2>

                    <p>{{ Str::limit($post->content, 120) }}</p>

                    @if($post->tags->isNotEmpty())
                        <div class="tag-list">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}" class="tag-badge">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">Read More</a>
                </article>
            @endforeach
        </div>

        <div class="pagination-wrap">
            {{ $posts->links() }}
        </div>
    @endif

@endsection
