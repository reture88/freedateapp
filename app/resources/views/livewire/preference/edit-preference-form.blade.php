<section class="panel">
    <h1>Dating preferences</h1>

    @if ($summaryDisplayText !== '')
        <p>{{ $summaryDisplayText }}</p>
    @endif

    @include('shared.flash')

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save" class="stack">
        <fieldset>
            <legend>Preferred age</legend>
            <label>
                Minimum age
                <input wire:model="preferredAgeMin" type="number" min="18" max="120" required>
            </label>
            <label>
                Maximum age
                <input wire:model="preferredAgeMax" type="number" min="18" max="120" required>
            </label>
        </fieldset>

        <label>
            Distance in km
            <input wire:model="preferredDistanceKm" type="number" min="1" max="500" required>
            <span class="field-help">Discovery prioritizes closer profiles when location data is available.</span>
        </label>

        <fieldset>
            <legend>Preferred genders</legend>
            @foreach ($genderOptions as $value => $label)
                <label class="checkbox-row">
                    <input type="checkbox" wire:model="preferredGenders" value="{{ $value }}">
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>

        <fieldset>
            <legend>Preferred orientations</legend>
            @foreach ($orientationOptions as $value => $label)
                <label class="checkbox-row">
                    <input type="checkbox" wire:model="preferredOrientations" value="{{ $value }}">
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>

        <fieldset>
            <legend>Relationship intent</legend>
            @foreach ($intentOptions as $value => $label)
                <label class="checkbox-row">
                    <input type="checkbox" wire:model="relationshipIntents" value="{{ $value }}">
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>

        <label class="checkbox-row">
            <input type="checkbox" wire:model="excludeAiWingmanEnabled">
            <span>
                Do not show profiles with AI Szarnyseged enabled
                <small>Use this if you only want conversations started by the two matched people.</small>
            </span>
        </label>

        <fieldset>
            <legend>Dealbreakers</legend>
            @foreach ($dealbreakerOptions as $value => $label)
                <label class="checkbox-row">
                    <input type="checkbox" wire:model="dealbreakers" value="{{ $value }}">
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>

        <fieldset>
            <legend>Soft preferences</legend>
            @foreach ($softPreferenceOptions as $value => $label)
                <label class="checkbox-row">
                    <input type="checkbox" wire:model="softPreferences" value="{{ $value }}">
                    {{ $label }}
                </label>
            @endforeach
        </fieldset>

        <button class="button button--primary" type="submit" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">Save preferences</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>
    </form>
</section>
