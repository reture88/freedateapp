@extends('layouts.auth')

@section('title', 'Create your account')

@section('content')
    @component('auth.partials.shell', [
        'title' => 'Create your account',
        'intro' => 'Just the basics for now.',
        'headline' => 'Start simple. Get to the good stuff next.',
        'copy' => 'Create your account in a minute. Your personality, interests and dating preferences belong in profile onboarding, not in signup.',
        'noteKind' => 'purple',
        'noteLabel' => 'MORE THAN A LOGIN',
        'noteTitle' => 'Account first. Context second.',
        'noteCopy' => 'After registration, MTAM moves naturally into dating-profile setup.',
    ])
        @slot('topAction')
            <a class="mtam-auth-top-action" href="{{ route('auth.login.show-form') }}">Log in</a>
        @endslot

        <form method="POST" action="{{ route('auth.register') }}" class="mtam-auth-form">
            @csrf

            @include('auth.partials.field', [
                'name' => 'name',
                'label' => 'Name',
                'placeholder' => 'Your name',
                'autocomplete' => 'name',
                'autofocus' => true,
            ])

            @include('auth.partials.field', [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'you@example.com',
                'autocomplete' => 'email',
            ])

            @include('auth.partials.field', [
                'name' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'placeholder' => 'Enter your password',
                'autocomplete' => 'new-password',
                'help' => 'At least 8 characters.',
            ])

            @include('auth.partials.field', [
                'name' => 'password_confirmation',
                'label' => 'Confirm password',
                'type' => 'password',
                'placeholder' => 'Confirm your password',
                'autocomplete' => 'new-password',
            ])

            <button type="submit" class="mtam-auth-button mtam-auth-button--primary">Create account</button>
        </form>

        <p class="mtam-auth-legal">
            By creating an account, you confirm that you are at least 18 years old and agree to the
            <a href="{{ route('legal.terms') }}">Terms of Service</a> and
            <a href="{{ route('legal.privacy') }}">Privacy Policy</a>.
        </p>

        <p class="mtam-auth-switch">
            Already have an account?
            <a href="{{ route('auth.login.show-form') }}">Log in</a>
        </p>

        <aside class="mtam-auth-next-note">
            <strong>Next step</strong>
            <span>You will set up your dating profile after your account is created.</span>
        </aside>
    @endcomponent
@endsection
