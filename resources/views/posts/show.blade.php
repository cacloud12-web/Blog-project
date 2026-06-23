@extends('layouts.blog')

@section('title', $post->title)

@section('content')

    <article class="post-detail">
        @if($post->featured_image)
            <img
                src="{{ Storage::url($post->featured_image) }}"
                alt="{{ $post->title }}"
                class="post-featured-image"
            >
        @endif

        <div class="post-detail-meta">
            By {{ $post->user->name }}
            &middot;
            {{ $post->created_at->format('M d, Y') }}
            @if($post->category)
                &middot; {{ $post->category->name }}
            @endif
            &middot; {{ $post->view_count }} views
        </div>

        <h1>{{ $post->title }}</h1>

        @if($post->tags->isNotEmpty())
            <div class="tag-list">
                @foreach($post->tags as $tag)
                    <span class="tag-badge">{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="post-detail-content">{{ $post->content }}</div>

        <div class="post-actions">
            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary">Edit</a>
            @endcan

            @can('delete', $post)
                <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            @endcan

            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back to Posts</a>
        </div>
    </article>

    <section class="comments-section">
        @include('comments.partials.list', ['post' => $post])
        @include('comments.partials.form', ['post' => $post])
    </section>

@endsection
