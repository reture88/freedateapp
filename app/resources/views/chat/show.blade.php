@extends('layouts.public')

@section('title', 'Conversation')

@section('content')
    <section class="panel">
        @php($primaryPhoto = $conversationView->otherProfile->photos()->where('is_primary', true)->orderBy('sort_order')->first() ?? $conversationView->otherProfile->photos()->orderBy('sort_order')->first())
        <header class="conversation-header">
            <a href="{{ route('profiles.show', ['profile' => $conversationView->otherProfile->getKey()]) }}" class="conversation-header__profile">
                @if ($primaryPhoto)
                    <img src="{{ $primaryPhoto->publicUrl() }}" alt="{{ $conversationView->otherProfile->display_name }} profile photo">
                @endif
                <span>{{ $conversationView->otherProfile->display_name }}</span>
            </a>
            <div class="conversation-header__actions">
                <form method="POST" action="{{ route('connections.unmatch', ['connection' => $conversationView->conversation->connection_id]) }}">
                    @csrf
                    @include('components.confirm-button', [
                        'label' => 'Unmatch',
                        'message' => 'Unmatch this person and close the chat?',
                    ])
                </form>
                <form method="POST" action="{{ route('blocks.submit') }}">
                    @csrf
                    <input type="hidden" name="blocked_user_id" value="{{ $conversationView->otherProfile->user_id }}">
                    <input type="hidden" name="reason_category" value="safety">
                    @include('components.confirm-button', [
                        'label' => 'Block user',
                        'message' => 'Block this user and close the chat?',
                    ])
                </form>
            </div>
        </header>

        @include('shared.form-errors')

        @forelse ($conversationView->conversation->messages()->orderBy('id')->get() as $message)
            @if ('assistant' === $message->sender_type)
                @include('components.assistant-message', ['message' => $message])
            @else
                <article>
                    <p>{{ $message->body }}</p>
                </article>
            @endif
            <article>
                <form method="POST" action="{{ route('reports.submit') }}">
                    @csrf
                    <input type="hidden" name="target_type" value="{{ 'assistant' === $message->sender_type ? 'assistant_message' : 'message' }}">
                    <input type="hidden" name="target_id" value="{{ $message->getKey() }}">
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

        <form method="POST" action="{{ route('conversations.messages.send', ['conversation' => $conversationView->conversation->getKey()]) }}" class="stack">
            @csrf
            <label>
                Message
                <textarea name="body" maxlength="2000" required>{{ old('body') }}</textarea>
            </label>
            <button type="submit">Send</button>
        </form>
    </section>
@endsection
