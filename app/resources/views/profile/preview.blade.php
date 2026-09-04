@extends('layouts.public')

@section('title', 'Dating profile preview')

@section('content')
    <section class="panel">
        @php($primaryPhoto = $profile->photos()->where('is_primary', true)->orderBy('sort_order')->first() ?? $profile->photos()->orderBy('sort_order')->first())
        @php($publicTags = $profile->tags()->wherePivot('visibility', 'public')->orderBy('profile_tags.label')->pluck('profile_tags.label')->all())

        @include('components.profile-card', [
            'displayName' => $profile->display_name,
            'age' => $profile->birthdate->age,
            'city' => $profile->city,
            'countryCode' => $profile->country_code,
            'relationshipIntent' => $profile->relationship_intent,
            'bio' => $profile->bio,
            'primaryPhotoUrl' => $primaryPhoto?->publicUrl(),
            'sharedTags' => $publicTags,
        ])

        @if ($profile->ai_wingman_enabled)
            <p class="muted">AI Szarnyseged enabled</p>
        @endif
        <form method="POST" action="{{ route('dating-profile.visibility.update') }}">
            @csrf
            <input type="hidden" name="visibility_status" value="{{ $profile->visibility_status === 'visible' ? 'hidden' : 'visible' }}">
            <button class="button button--primary" type="submit">{{ $profile->visibility_status === 'visible' ? 'Hide profile' : 'Show profile' }}</button>
        </form>
        <div class="inline-actions">
            <a class="button button--secondary" href="{{ route('dating-profile.onboarding.show') }}">Edit dating profile</a>
            <a class="button button--secondary" href="{{ route('dating-preferences.edit') }}">Edit dating preferences</a>
        </div>
    </section>
@endsection
