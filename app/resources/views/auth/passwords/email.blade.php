@extends('layouts.auth')

@section('title', 'Forgot password')

@section('content')
    @component('auth.partials.shell', [
        'title' => 'Forgot your password?',
        'intro' => 'We will send reset instructions if the account exists.',
        'headline' => 'Reset without the guesswork.',
        'copy' => 'Enter your email and we will handle the rest. MTAM uses the same response whether or not an account exists for that address.',
        'noteKind' => 'purple',
        'noteLabel' => 'PRIVACY-SAFE BY DEFAULT',
        'noteTitle' => 'No account enumeration.',
        'noteCopy' => 'Only invalid email format should produce a field error here.',
    ])
        @slot('topAction')
            <a class="mtam-auth-top-action" href="{{ route('auth.login.show-form') }}">Back to log in</a>
        @endslot

        <form method="POST" action="{{ route('password.email') }}" class="mtam-auth-form">
            @csrf

            @include('auth.partials.field', [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'you@example.com',
                'autocomplete' => 'email',
                'autofocus' => true,
            ])

            <button type="submit" class="mtam-auth-button mtam-auth-button--primary">Send reset link</button>
        </form>

        @if (session('status'))
            @include('auth.partials.notice', [
                'kind' => 'info',
                'title' => 'Check your inbox',
                'message' => session('status'),
            ])
        @endif

        <a class="mtam-auth-text-action mtam-auth-text-action--standalone" href="{{ route('auth.login.show-form') }}">Back to log in</a>
    @endcomponent
@endsection
