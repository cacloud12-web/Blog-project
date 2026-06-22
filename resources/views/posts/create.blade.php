@extends('layouts.blog')

@section('title', 'Create Post')

@section('content')

    <h1 style="margin-bottom:20px; font-size:22px;">Create New Post</h1>

    {{-- Form saves to PostController@store --}}
    <div class="form-box">
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    value="{{ old('title') }}"
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
                >{{ old('content') }}</textarea>
                @error('content')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Post</button>
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection
