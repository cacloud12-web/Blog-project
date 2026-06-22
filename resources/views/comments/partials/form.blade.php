@auth
    <div class="comment-form-box">
        <h3>Write a Comment</h3>
        <form action="{{ route('comments.store', $post) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="comment">Comment</label>
                <textarea
                    id="comment"
                    name="comment"
                    class="form-control"
                    rows="4"
                    required
                >{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Post Comment</button>
        </form>
    </div>
@else
    <div class="login-prompt">
        Please <a href="{{ route('login') }}">login</a> to leave a comment.
    </div>
@endauth
