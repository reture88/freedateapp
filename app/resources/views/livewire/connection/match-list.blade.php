<section class="panel">
    <h1>Matches</h1>

    <p class="alert alert-error" wire:offline>
        Reconnecting. New matches may appear after the connection returns.
    </p>

    <div class="match-list">
        @forelse ($matches as $match)
            <article class="match-list__item">
                <h2>{{ $match['display_name'] }}</h2>
                <p>{{ $match['city'] }}</p>
                <a class="button button--primary" href="{{ route('conversations.show', ['conversation' => $match['conversation_id']]) }}">Open chat</a>
                <form method="POST" action="{{ route('connections.unmatch', ['connection' => $match['connection_id']]) }}">
                    @csrf
                    @include('components.confirm-button', [
                        'label' => 'Unmatch',
                        'message' => 'Unmatch this person and close the chat?',
                    ])
                </form>
            </article>
        @empty
            @include('components.empty-state', [
                'title' => 'No matches yet.',
                'message' => 'Accept profiles in discovery. When someone accepts you back, the chat opens here.',
                'actionUrl' => route('discovery.show'),
                'actionLabel' => 'Go to discovery',
                'class' => 'empty-state',
            ])
        @endforelse
    </div>
</section>
