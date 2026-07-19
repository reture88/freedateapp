@extends('layouts.public')

@section('title', 'Conversation')

@section('content')
    <section class="panel">
        <h1>{{ $conversationView->otherProfile->display_name }}</h1>
        <form method="POST" action="{{ route('connections.unmatch', ['connection' => $conversationView->conversation->connection_id]) }}">
            @csrf
            <button type="submit">Unmatch</button>
        </form>

        @include('shared.form-errors')

        @forelse ($conversationView->conversation->messages()->orderBy('created_at')->get() as $message)
            <article>
                <p>{{ $message->body }}</p>
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
