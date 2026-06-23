@extends('layouts.blog')

@section('title', 'Create Post')

@section('content')

    <h1 style="margin-bottom:20px; font-size:22px;">Create New Post</h1>

    <div class="form-box">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
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

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" class="form-control">
                    <option value="">No category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="tags">Tags</label>
                <input
                    type="text"
                    id="tags"
                    name="tags"
                    class="form-control"
                    value="{{ old('tags') }}"
                    placeholder="laravel, php, tutorial"
                >
                <p class="form-hint">Separate tags with commas.</p>
                @error('tags')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input
                    type="file"
                    id="featured_image"
                    name="featured_image"
                    class="form-control"
                    accept="image/jpeg,image/png,image/webp"
                >
                @error('featured_image')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="draft" @selected(old('status') == 'draft')>Draft</option>
                    <option value="published" @selected(old('status') == 'published')>Published</option>
                </select>
                @error('status')
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
