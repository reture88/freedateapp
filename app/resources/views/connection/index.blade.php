@extends('layouts.public')

@section('title', 'Matches')

@section('content')
    <section class="panel">
        <h1>Matches</h1>

        @forelse ($matches as $match)
            <article>
                <h2>{{ $match->displayName }}</h2>
                <p>{{ $match->city }}</p>
                <a href="{{ route('conversations.show', ['conversation' => $match->conversationId]) }}">Open chat</a>
                <form method="POST" action="{{ route('connections.unmatch', ['connection' => $match->connectionId]) }}">
                    @csrf
                    <button type="submit">Unmatch</button>
                </form>
            </article>
        @empty
            <p>No matches yet.</p>
        @endforelse
    </section>
@endsection
