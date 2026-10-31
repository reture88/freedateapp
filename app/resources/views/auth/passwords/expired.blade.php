@extends('layouts.auth')

@section('title', 'Reset link expired')

@section('content')
    <section class="mtam-status-page">
        <header class="mtam-auth-flow__top">
            @include('auth.partials.brand')
        </header>

        <article class="mtam-status-card mtam-status-card--reset">
            <p class="mtam-status-card__eyebrow">PASSWORD RESET</p>
            <span class="mtam-status-card__icon" aria-hidden="true">!</span>
            <h1>This reset link is no longer valid</h1>
            <p>For your security, password reset links expire. Request a new link and we will send instructions if the account exists.</p>
            @if (session('error'))
                @include('auth.partials.notice', [
                    'kind' => 'error',
                    'title' => 'Reset link unavailable',
                    'message' => session('error'),
                ])
            @endif
            <a class="mtam-auth-button mtam-auth-button--primary" href="{{ route('password.request') }}">Request new link</a>
            <a class="mtam-auth-button mtam-auth-button--secondary" href="{{ route('auth.login.show-form') }}">Back to log in</a>
        </article>
    </section>
@endsection
