<section class="panel">
    <header class="conversation-header">
        <a href="{{ route('profiles.show', ['profile' => $otherProfile['id']]) }}" class="conversation-header__profile">
            @if ($otherProfile['primary_photo_url'])
                <img src="{{ $otherProfile['primary_photo_url'] }}" alt="{{ $otherProfile['display_name'] }} profile photo">
            @endif
            <span>{{ $otherProfile['display_name'] }}</span>
        </a>
        <div class="conversation-header__actions">
            <form method="POST" action="{{ route('connections.unmatch', ['connection' => $connectionId]) }}">
                @csrf
                @include('components.confirm-button', [
                    'label' => 'Unmatch',
                    'message' => 'Unmatch this person and close the chat?',
                ])
            </form>
            <form method="POST" action="{{ route('blocks.submit') }}">
                @csrf
                <input type="hidden" name="blocked_user_id" value="{{ $otherProfile['user_id'] }}">
                <input type="hidden" name="reason_category" value="safety">
                @include('components.confirm-button', [
                    'label' => 'Block user',
                    'message' => 'Block this user and close the chat?',
                ])
            </form>
        </div>
    </header>

    <p class="alert alert-error" wire:offline>
        Reconnecting. Messages will keep working after the connection returns.
    </p>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @forelse ($messages as $message)
        @if ('assistant' === $message['sender_type'])
            @include('components.assistant-message', ['message' => (object) $message])
        @else
            <article>
                <p>{{ $message['body'] }}</p>
                @if ($message['sender_user_id'] === $userId && $message['read_at'])
                    <p class="muted">Seen</p>
                @endif
            </article>
        @endif
        <article>
            <form method="POST" action="{{ route('reports.submit') }}">
                @csrf
                <input type="hidden" name="target_type" value="{{ 'assistant' === $message['sender_type'] ? 'assistant_message' : 'message' }}">
                <input type="hidden" name="target_id" value="{{ $message['id'] }}">
                <input type="hidden" name="category" value="message_concern">
                @include('components.confirm-button', [
                    'label' => 'Report message',
                    'message' => 'Submit a safety report for this message?',
                    'class' => 'button button--secondary',
                ])
            </form>
        </article>
    @empty
        @include('components.empty-state', [
            'title' => 'No messages yet.',
            'message' => 'Send a short first message when you are ready.',
        ])
    @endforelse

    <form wire:submit.prevent="send" class="stack">
        <label>
            Message
            <textarea wire:model="body" maxlength="2000" required></textarea>
            <span class="field-help">Messages can be up to 2000 characters.</span>
        </label>
        <button class="button button--primary" type="submit" wire:loading.attr="disabled" wire:target="send">
            <span wire:loading.remove wire:target="send">Send</span>
            <span wire:loading wire:target="send">Sending...</span>
        </button>
    </form>
</section>
