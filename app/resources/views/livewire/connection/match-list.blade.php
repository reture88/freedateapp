<section class="panel match-screen">
    <header class="match-screen__header">
        <h1>Matches</h1>
        <p>{{ count($matches) }} {{ \Illuminate\Support\Str::plural('person', count($matches)) }} you've matched with</p>
    </header>

    <p class="alert alert-error" wire:offline>
        Reconnecting. New matches may appear after the connection returns.
    </p>

    <div class="match-list">
        @forelse ($matches as $match)
            @php
                $name = $match['age'] ? sprintf('%s, %d', $match['display_name'], $match['age']) : $match['display_name'];
                $state = $match['state'] ?? 'default';
            @endphp
            <article class="match-list__item match-list__item--{{ $state }}">
                <a class="match-list__row" href="{{ route('conversations.show', ['conversation' => $match['conversation_id']]) }}">
                    @if ($match['primary_photo_url'])
                        <img class="match-list__avatar" src="{{ $match['primary_photo_url'] }}" alt="">
                    @else
                        <span class="match-list__avatar match-list__avatar--placeholder" aria-hidden="true">{{ strtoupper(substr($match['display_name'], 0, 1)) }}</span>
                    @endif
                    <span class="match-list__copy">
                        <strong>{{ $name }}</strong>
                        <small>{{ $match['status_label'] }}</small>
                    </span>
                    @if ('new' === $state || 'unread' === $state)
                        <span class="match-list__state-dot" aria-hidden="true"></span>
                    @endif
                    <span class="sr-only">Open chat</span>
                </a>

                <form method="POST" action="{{ route('connections.unmatch', ['connection' => $match['connection_id']]) }}">
                    @csrf
                    @include('components.confirm-button', [
                        'label' => 'Unmatch',
                        'message' => 'Unmatch this person and close the chat?',
                        'class' => 'button button--text match-list__unmatch',
                    ])
                </form>
            </article>
        @empty
            <div class="match-screen__empty">
                @include('components.empty-state', [
                    'title' => 'No matches yet.',
                    'message' => 'Accept profiles in discovery. When someone accepts you back, the chat opens here.',
                    'actionUrl' => route('discovery.show'),
                    'actionLabel' => 'Go to discovery',
                    'class' => 'empty-state',
                ])
            </div>
        @endforelse
    </div>

    <p class="match-screen__legend">New matches are yellow. Unread matches are purple.</p>
</section>
