<h2>Comments ({{ $post->comments->count() }})</h2>

@if($post->comments->isEmpty())
    <p style="color:#777; font-size:14px;">No comments yet.</p>
@else
    @foreach($post->comments as $comment)
        <div class="comment-item {{ $comment->is_approved ? '' : 'comment-pending' }}">
            <div class="comment-meta">
                <strong>{{ $comment->user->name }}</strong>
                &middot;
                {{ $comment->created_at->format('M d, Y h:i A') }}
                @unless($comment->is_approved)
                    <span class="pending-badge">Pending approval</span>
                @endunless
            </div>
            <p class="comment-text">{{ $comment->comment }}</p>

            @can('approve', $comment)
                @unless($comment->is_approved)
                    <form action="{{ route('comments.approve', $comment) }}" method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="btn btn-primary">Approve</button>
                    </form>
                @endunless

                <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove</button>
                </form>
            @endcan
        </div>
    @endforeach
@endif
