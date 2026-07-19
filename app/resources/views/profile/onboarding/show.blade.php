@extends('layouts.public')

@section('title', $profile ? 'Edit dating profile' : 'Dating profile onboarding')

@section('content')
    <section class="panel">
        <h1>{{ $profile ? 'Edit dating profile' : 'Dating profile onboarding' }}</h1>
        <p>Create the profile other people will see in discovery.</p>

        @include('shared.form-errors')

        <form method="POST" action="{{ $profile ? route('dating-profile.update') : route('dating-profile.onboarding.save') }}" class="stack">
            @csrf

            <label>
                Display name
                <input name="display_name" value="{{ old('display_name', $profile?->display_name) }}" required maxlength="80">
            </label>

            <label>
                Birthdate
                <input type="date" name="birthdate" value="{{ old('birthdate', $profile?->birthdate?->toDateString()) }}" required>
            </label>

            <label>
                Gender
                <select name="gender" required>
                    @foreach (['man' => 'Man', 'woman' => 'Woman', 'non_binary' => 'Non-binary'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('gender', $profile?->gender) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                Orientation
                <select name="orientation" required>
                    @foreach (['straight' => 'Straight', 'gay' => 'Gay', 'bisexual' => 'Bisexual', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('orientation', $profile?->orientation) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                City
                <input name="city" value="{{ old('city', $profile?->city) }}" required maxlength="120">
            </label>

            <label>
                Country code
                <input name="country_code" value="{{ old('country_code', $profile?->country_code ?? 'HU') }}" required maxlength="2">
            </label>

            <label>
                Approximate latitude
                <input name="approximate_latitude" value="{{ old('approximate_latitude', $profile?->approximate_latitude) }}">
            </label>

            <label>
                Approximate longitude
                <input name="approximate_longitude" value="{{ old('approximate_longitude', $profile?->approximate_longitude) }}">
            </label>

            <label>
                Relationship intent
                <select name="relationship_intent" required>
                    @foreach (['long_term' => 'Long-term relationship', 'short_term' => 'Short-term dating', 'friendship' => 'Friendship first', 'unsure' => 'Not sure yet'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('relationship_intent', $profile?->relationship_intent) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                Bio
                <textarea name="bio" maxlength="1000">{{ old('bio', $profile?->bio) }}</textarea>
            </label>

            <label class="checkbox-row">
                <input type="checkbox" name="ai_wingman_enabled" value="1" @checked((bool) old('ai_wingman_enabled', $profile?->ai_wingman_enabled ?? false))>
                Enable AI Szarnyseged playful starter messages
            </label>

            <label class="checkbox-row">
                <input type="checkbox" name="privacy_consent" value="1" required>
                I understand my visible profile details can be used for matching and discovery.
            </label>

            <button type="submit">Save dating profile</button>
        </form>

        @if ($profile)
            <section class="profile-photo-manager">
                <h2>Profile photos</h2>
                <p class="muted">Upload up to 7 photos and choose which one appears first in discovery.</p>

                <form method="POST" action="{{ route('dating-profile.photos.upload') }}" enctype="multipart/form-data" class="stack">
                    @csrf
                    <label>
                        Add photo
                        <input type="file" name="photo" accept="image/*" required>
                    </label>
                    @error('photo')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="button button--secondary">Upload photo</button>
                </form>

                @if ($profile->photos->isNotEmpty())
                    <div class="profile-photo-grid">
                        @foreach ($profile->photos->sortBy('sort_order') as $photo)
                            <article class="profile-photo-card">
                                <img src="{{ $photo->publicUrl() }}" alt="{{ $profile->display_name }} profile photo">
                                <div class="profile-photo-card__footer">
                                    @if ($photo->is_primary)
                                        <span class="status-pill">Primary</span>
                                    @else
                                        <form method="POST" action="{{ route('dating-profile.photos.primary', ['photo' => $photo->getKey()]) }}">
                                            @csrf
                                            <button type="submit" class="button button--secondary">Make primary</button>
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </section>
@endsection
