@extends('layouts.auth')

@section('title', 'Account unavailable')

@section('content')
    <section class="mtam-status-page">
        <header class="mtam-auth-flow__top">
            @include('auth.partials.brand')
        </header>

        <article class="mtam-status-card">
            <p class="mtam-status-card__eyebrow">ACCOUNT ACCESS</p>
            <span class="mtam-status-card__icon" aria-hidden="true">-</span>
            @if ($status)
                <p class="mtam-status-card__eyebrow">{{ $status->label() }}</p>
            @endif
            <h1>{{ $headline }}</h1>
            <p>{{ $message }}</p>
            @auth
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button class="mtam-auth-button mtam-auth-button--secondary" type="submit">Log out</button>
                </form>
            @else
                <a class="mtam-auth-button mtam-auth-button--secondary" href="{{ route('auth.login.show-form') }}">Back to log in</a>
            @endauth
        </article>

        <p class="mtam-status-page__note">Support and appeal actions need product policy and backend capability before they are shown here.</p>
    </section>
@endsection
