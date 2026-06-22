<h2>Comments ({{ $post->comments->count() }})</h2>

@if($post->comments->isEmpty())
    <p style="color:#777; font-size:14px;">No comments yet.</p>
@else
    @foreach($post->comments as $comment)
        <div class="comment-item">
            <div class="comment-meta">
                <strong>{{ $comment->user->name }}</strong>
                &middot;
                {{ $comment->created_at->format('M d, Y h:i A') }}
            </div>
            <p class="comment-text">{{ $comment->comment }}</p>
        </div>
    @endforeach
@endif
