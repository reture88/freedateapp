@extends('layouts.auth')

@section('title', 'Verify your email')

@section('content')
    <section class="mtam-status-page">
        <header class="mtam-auth-flow__top">
            @include('auth.partials.brand')
        </header>

        <article class="mtam-status-card">
            <p class="mtam-status-card__eyebrow">EMAIL VERIFICATION</p>
            <span class="mtam-status-card__icon" aria-hidden="true">@</span>
            <h1>Check your email</h1>
            <p>We sent a verification link to <strong>{{ $user->email }}</strong>. Open that link to continue to your MTAM profile setup.</p>

            @if (session('status'))
                @include('auth.partials.notice', [
                    'kind' => 'success',
                    'title' => 'Email sent',
                    'message' => session('status'),
                ])
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button class="mtam-auth-button mtam-auth-button--primary" type="submit">Resend verification email</button>
            </form>

            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button class="mtam-auth-button mtam-auth-button--secondary" type="submit">Log out</button>
            </form>
        </article>

        <p class="mtam-status-page__note">Email changes are managed from account settings after verification.</p>
    </section>
@endsection
