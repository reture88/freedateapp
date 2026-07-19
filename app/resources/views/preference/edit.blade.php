@extends('layouts.public')

@section('title', 'Dating preferences')

@section('content')
    <section class="panel">
        <h1>Dating preferences</h1>
        @include('shared.preference.summary', ['summary' => $summary])
        @include('shared.form-errors')

        <form method="POST" action="{{ route('dating-preferences.save') }}" class="stack">
            @csrf

            <fieldset>
                <legend>Preferred age</legend>
                <label>
                    Minimum age
                    <input name="preferred_age_min" type="number" min="18" max="120" value="{{ old('preferred_age_min', $preference->preferred_age_min) }}" required>
                </label>
                <label>
                    Maximum age
                    <input name="preferred_age_max" type="number" min="18" max="120" value="{{ old('preferred_age_max', $preference->preferred_age_max) }}" required>
                </label>
            </fieldset>

            <label>
                Distance in km
                <input name="preferred_distance_km" type="number" min="1" max="500" value="{{ old('preferred_distance_km', $preference->preferred_distance_km) }}" required>
            </label>

            <fieldset>
                <legend>Preferred genders</legend>
                @foreach (['woman' => 'Woman', 'man' => 'Man', 'non_binary' => 'Non-binary', 'other' => 'Other'] as $value => $label)
                    <label class="checkbox-row">
                        <input type="checkbox" name="preferred_genders[]" value="{{ $value }}" @checked(in_array($value, old('preferred_genders', $preference->preferred_genders), true))>
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>

            <fieldset>
                <legend>Preferred orientations</legend>
                @foreach (['straight' => 'Straight', 'gay' => 'Gay', 'lesbian' => 'Lesbian', 'bisexual' => 'Bisexual', 'pansexual' => 'Pansexual', 'queer' => 'Queer', 'other' => 'Other'] as $value => $label)
                    <label class="checkbox-row">
                        <input type="checkbox" name="preferred_orientations[]" value="{{ $value }}" @checked(in_array($value, old('preferred_orientations', $preference->preferred_orientations), true))>
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>

            <fieldset>
                <legend>Relationship intent</legend>
                @foreach (['long_term' => 'Long-term relationship', 'short_term' => 'Short-term dating', 'friendship' => 'Friendship first', 'unsure' => 'Not sure yet'] as $value => $label)
                    <label class="checkbox-row">
                        <input type="checkbox" name="relationship_intents[]" value="{{ $value }}" @checked(in_array($value, old('relationship_intents', $preference->relationship_intents), true))>
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>

            <label class="checkbox-row">
                <input type="checkbox" name="exclude_ai_wingman_enabled" value="1" @checked((bool) old('exclude_ai_wingman_enabled', $preference->exclude_ai_wingman_enabled))>
                Do not show profiles with AI Szarnyseged enabled
            </label>

            <fieldset>
                <legend>Dealbreakers</legend>
                @foreach (['smoker' => 'Smoker', 'no_kids' => 'Does not want kids', 'far_away' => 'Too far away'] as $value => $label)
                    <label class="checkbox-row">
                        <input type="checkbox" name="dealbreakers[]" value="{{ $value }}" @checked(in_array($value, old('dealbreakers', $preference->dealbreakers ?? []), true))>
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>

            <fieldset>
                <legend>Soft preferences</legend>
                @foreach (['pets' => 'Pets', 'weekend_trips' => 'Weekend trips', 'fitness' => 'Fitness'] as $value => $label)
                    <label class="checkbox-row">
                        <input type="checkbox" name="soft_preferences[]" value="{{ $value }}" @checked(in_array($value, old('soft_preferences', $preference->soft_preferences ?? []), true))>
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>

            <button type="submit">Save preferences</button>
        </form>
    </section>
@endsection
