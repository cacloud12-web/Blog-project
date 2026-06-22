@extends('layouts.blog')

@section('title', $post->title)

@section('content')

    {{-- Full post content --}}
    <article class="post-detail">
        <div class="post-detail-meta">
            By {{ $post->user->name }}
            &middot;
            {{ $post->created_at->format('M d, Y') }}
        </div>

        <h1>{{ $post->title }}</h1>

        <div class="post-detail-content">{{ $post->content }}</div>

        {{-- Edit and Delete buttons (only for post owner) --}}
        <div class="post-actions">
            @auth
                @if(auth()->id() === $post->user_id)
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary">Edit</a>

                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                @endif
            @endauth

            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back to Posts</a>
        </div>
    </article>

    {{-- Comments section --}}
    <section class="comments-section">
        @include('comments.partials.list', ['post' => $post])
        @include('comments.partials.form', ['post' => $post])
    </section>

@endsection
