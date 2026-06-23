@extends('layouts.blog')

@section('title', 'Manage Categories & Tags')

@section('content')
    <h1 style="margin-bottom:20px; font-size:22px;">Manage Categories & Tags</h1>

    <div class="form-box" style="margin-bottom:30px;">
        <h2 style="font-size:16px; margin-bottom:12px;">Add Category</h2>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Category name</label>
                <input type="text" id="name" name="name" class="form-control" required>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
    </div>

    <div class="admin-section">
        <h2>Categories</h2>
        @forelse($categories as $category)
            <div class="admin-item">
                <span>{{ $category->name }} ({{ $category->posts_count }} posts)</span>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        @empty
            <p>No categories yet.</p>
        @endforelse
    </div>

    <div class="admin-section">
        <h2>Tags</h2>
        @forelse($tags as $tag)
            <div class="admin-item">
                <span>{{ $tag->name }} ({{ $tag->posts_count }} posts)</span>
                <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        @empty
            <p>No tags yet.</p>
        @endforelse
    </div>
@endsection
