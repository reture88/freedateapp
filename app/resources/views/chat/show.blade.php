@extends('layouts.public')

@section('title', 'Conversation')

@section('content')
    <livewire:chat.conversation-panel :conversation-view="$conversationView" />
@endsection
