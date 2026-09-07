@extends('layouts.public')

@section('title', 'Messages')
@section('body_class', 'app-shell public-shell public-shell--discovery public-shell--messages')
@section('brand_label', 'MTAM')

@section('topbar_nav')
    <nav class="topbar__nav topbar__nav--discovery" aria-label="Messages actions">
        <a class="button button--secondary button--compact" href="{{ route('profile.show') }}">Profile</a>
    </nav>
@endsection

@section('content')
    <livewire:chat.message-list :conversations="$conversations" />

    @include('components.mobile-bottom-nav', ['active' => 'messages'])
@endsection
