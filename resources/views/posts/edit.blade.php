@extends('layouts.blog')

@section('title', 'Edit Post')

@section('content')

    <h1 style="margin-bottom:20px; font-size:22px;">Edit Post</h1>

    {{-- Form sends PUT request to PostController@update --}}
    <div class="form-box">
        <form action="{{ route('posts.update', $post) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    value="{{ old('title', $post->title) }}"
                    required
                >
                @error('title')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea
                    id="content"
                    name="content"
                    class="form-control"
                    required
                >{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection
