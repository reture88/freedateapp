@extends('layouts.public')

@section('title', 'Conversation')

@section('content')
    <section class="panel">
        <h1>{{ $conversationView->otherProfile->display_name }}</h1>
        <form method="POST" action="{{ route('connections.unmatch', ['connection' => $conversationView->conversation->connection_id]) }}">
            @csrf
            <button type="submit">Unmatch</button>
        </form>
        <form method="POST" action="{{ route('blocks.submit') }}">
            @csrf
            <input type="hidden" name="blocked_user_id" value="{{ $conversationView->otherProfile->user_id }}">
            <input type="hidden" name="reason_category" value="safety">
            <button type="submit">Block user</button>
        </form>

        @include('shared.form-errors')

        @forelse ($conversationView->conversation->messages()->orderBy('created_at')->get() as $message)
            <article>
                @if ('assistant' === $message->sender_type)
                    <strong>AI Szarnyseged</strong>
                @endif
                <p>{{ $message->body }}</p>
                <form method="POST" action="{{ route('reports.submit') }}">
                    @csrf
                    <input type="hidden" name="target_type" value="{{ 'assistant' === $message->sender_type ? 'assistant_message' : 'message' }}">
                    <input type="hidden" name="target_id" value="{{ $message->getKey() }}">
                    <input type="hidden" name="category" value="message_concern">
                    <button type="submit">Report message</button>
                </form>
            </article>
        @empty
            <p>No messages yet.</p>
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
