@extends('layouts.public')

@section('title', 'Dating profile preview')

@section('content')
    <section class="panel">
        <h1>{{ $profile->display_name }}</h1>
        <p>{{ $profile->city }}, {{ $profile->country_code }}</p>
        <p>{{ $profile->relationship_intent }}</p>
        <p>{{ $profile->bio }}</p>
        @if ($profile->ai_wingman_enabled)
            <p>AI Szarnyseged enabled</p>
        @endif
        <form method="POST" action="{{ route('dating-profile.visibility.update') }}">
            @csrf
            <input type="hidden" name="visibility_status" value="{{ $profile->visibility_status === 'visible' ? 'hidden' : 'visible' }}">
            <button type="submit">{{ $profile->visibility_status === 'visible' ? 'Hide profile' : 'Show profile' }}</button>
        </form>
        <a href="{{ route('dating-profile.onboarding.show') }}">Edit dating profile</a>
    </section>
@endsection
