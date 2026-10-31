@extends('layouts.auth')

@section('title', 'Log in')

@section('content')
    @component('auth.partials.shell', [
        'title' => 'Welcome back',
        'intro' => 'Log in to continue.',
        'headline' => 'Come back to more context.',
        'copy' => 'Your matches, conversations and compatibility context are waiting where you left them.',
        'noteKind' => 'yellow',
        'noteTitle' => 'Less guessing. Better context.',
        'noteCopy' => 'MTAM keeps personality and compatibility close to the dating decision.',
    ])
        @slot('topAction')
            <a class="mtam-auth-top-action" href="{{ route('auth.register.show-form') }}">Sign up</a>
        @endslot

        <form method="POST" action="{{ route('auth.login') }}" class="mtam-auth-form">
            @csrf

            @include('auth.partials.field', [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'you@example.com',
                'autocomplete' => 'email',
                'autofocus' => true,
            ])

            @include('auth.partials.field', [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'placeholder' => 'Enter your password',
                'autocomplete' => 'current-password',
            ])

            <div class="mtam-auth-form__row">
                <label class="mtam-auth-check">
                    <input name="remember" type="checkbox" value="1" @checked(old('remember'))>
                    <span>Remember me</span>
                </label>
                <a class="mtam-auth-text-action" href="{{ route('password.request') }}">Forgot password?</a>
            </div>

            <button type="submit" class="mtam-auth-button mtam-auth-button--primary">Log in</button>
        </form>

        <p class="mtam-auth-switch">
            New to MTAM?
            <a href="{{ route('auth.register.show-form') }}">Sign up</a>
        </p>

        <p class="mtam-auth-footnote">Account access states and recovery use calm, explicit messaging.</p>
    @endcomponent
@endsection
