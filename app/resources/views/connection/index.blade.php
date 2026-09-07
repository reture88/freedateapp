@extends('layouts.public')

@section('title', 'Matches')
@section('body_class', 'app-shell public-shell public-shell--discovery public-shell--matches')
@section('brand_label', 'MTAM')

@section('topbar_nav')
    <nav class="topbar__nav topbar__nav--discovery" aria-label="Matches actions">
        <a class="button button--secondary button--compact" href="{{ route('profile.show') }}">Profile</a>
    </nav>
@endsection

@section('content')
    <livewire:connection.match-list :matches="$matches" />

    @include('components.mobile-bottom-nav', ['active' => 'matches'])
@endsection
