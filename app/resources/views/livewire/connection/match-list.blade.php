<section class="panel">
    <h1>Matches</h1>

    <p class="alert alert-error" wire:offline>
        Reconnecting. New matches may appear after the connection returns.
    </p>

    @forelse ($matches as $match)
        <article>
            <h2>{{ $match['display_name'] }}</h2>
            <p>{{ $match['city'] }}</p>
            <a href="{{ route('conversations.show', ['conversation' => $match['conversation_id']]) }}">Open chat</a>
            <form method="POST" action="{{ route('connections.unmatch', ['connection' => $match['connection_id']]) }}">
                @csrf
                <button type="submit">Unmatch</button>
            </form>
        </article>
    @empty
        <p>No matches yet.</p>
    @endforelse
</section>
