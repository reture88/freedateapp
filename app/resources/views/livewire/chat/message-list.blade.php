<section class="panel message-screen">
    <header class="message-screen__header">
        <h1>Messages</h1>
        <p>Conversations with your matches</p>
    </header>

    <p class="alert alert-error" wire:offline>
        Reconnecting. Messages will keep working after the connection returns.
    </p>

    <div class="message-screen__section-label">Recent</div>

    <div class="message-list">
        @forelse ($conversations as $conversation)
            @php
                $name = $conversation['age'] ? sprintf('%s, %d', $conversation['display_name'], $conversation['age']) : $conversation['display_name'];
                $state = $conversation['state'] ?? 'default';
            @endphp
            <a class="message-list__item message-list__item--{{ $state }}" href="{{ route('conversations.show', ['conversation' => $conversation['conversation_id']]) }}">
                @if ($conversation['primary_photo_url'])
                    <img class="message-list__avatar" src="{{ $conversation['primary_photo_url'] }}" alt="">
                @else
                    <span class="message-list__avatar message-list__avatar--placeholder" aria-hidden="true">{{ strtoupper(substr($conversation['display_name'], 0, 1)) }}</span>
                @endif
                <span class="message-list__copy">
                    <strong>{{ $name }}</strong>
                    <small>{{ $conversation['preview_label'] }}</small>
                </span>
                @if ('unread' === $state)
                    <span class="message-list__state-dot" aria-hidden="true"></span>
                @endif
            </a>
        @empty
            <div class="message-screen__empty">
                @include('components.empty-state', [
                    'title' => 'No messages yet.',
                    'message' => 'When a matched conversation starts, it appears here.',
                    'actionUrl' => route('matches.index'),
                    'actionLabel' => 'View matches',
                    'class' => 'empty-state',
                ])
            </div>
        @endforelse
    </div>

    <section class="message-screen__guidance" aria-label="Conversation guidance">
        <h2>Keep it human.</h2>
        <p>Conversation starters stay inside profile context - not as chat automation.</p>
    </section>
</section>
