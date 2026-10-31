@extends('layouts.auth')

@section('title', 'Set a new password')

@section('content')
    @component('auth.partials.shell', [
        'title' => 'Set a new password',
        'intro' => 'Use at least 8 characters.',
        'headline' => 'Choose a new password.',
        'copy' => 'Keep requirements short and visible. If the confirmation does not match, the error stays attached to that field.',
        'panelClass' => 'mtam-auth-panel--compact',
    ])
        @slot('topAction')
            <a class="mtam-auth-top-action" href="{{ route('auth.login.show-form') }}">Back to log in</a>
        @endslot

        <form method="POST" action="{{ route('password.update') }}" class="mtam-auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            @include('auth.partials.field', [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'value' => old('email', $email),
                'placeholder' => 'you@example.com',
                'autocomplete' => 'email',
            ])

            @include('auth.partials.field', [
                'name' => 'password',
                'label' => 'New password',
                'type' => 'password',
                'placeholder' => 'Enter your new password',
                'autocomplete' => 'new-password',
                'help' => 'At least 8 characters.',
            ])

            @include('auth.partials.field', [
                'name' => 'password_confirmation',
                'label' => 'Confirm password',
                'type' => 'password',
                'placeholder' => 'Confirm your new password',
                'autocomplete' => 'new-password',
            ])

            <button type="submit" class="mtam-auth-button mtam-auth-button--primary">Update password</button>
        </form>

        <a class="mtam-auth-text-action mtam-auth-text-action--standalone" href="{{ route('auth.login.show-form') }}">Back to log in</a>
    @endcomponent
@endsection
