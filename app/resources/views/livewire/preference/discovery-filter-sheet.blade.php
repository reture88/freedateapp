<dialog class="filter-sheet" id="discovery-filter-sheet" aria-labelledby="discovery-filter-sheet-title" onclick="if (event.target === this) this.close()" wire:ignore.self>
    <form class="filter-sheet__panel" wire:submit.prevent="showMatches" aria-label="Discovery filters">
        <div class="filter-sheet__handle" aria-hidden="true"></div>

        <header class="filter-sheet__header">
            <h2 id="discovery-filter-sheet-title">Filters</h2>
            <button class="filter-sheet__reset" type="button" wire:click="resetFilters" wire:loading.attr="disabled" wire:target="resetFilters">
                Reset
            </button>
        </header>

        @if ($errors->any())
            <div class="filter-sheet__error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="filter-sheet__group" aria-labelledby="filter-distance-title">
            <h3 id="filter-distance-title">Distance</h3>
            <div class="filter-sheet__choice-grid filter-sheet__choice-grid--distance">
                @foreach ($distanceOptions as $value => $label)
                    <label class="filter-sheet__choice">
                        <input type="radio" wire:model="preferredDistanceKm" value="{{ $value }}">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="filter-sheet__group" aria-labelledby="filter-age-title">
            <div class="filter-sheet__age-header">
                <h3 id="filter-age-title">Age range</h3>
                <strong>{{ $preferredAgeMin }} - {{ $preferredAgeMax }}</strong>
            </div>
            <div class="filter-sheet__range-pair">
                <label>
                    <span>Minimum age</span>
                    <input type="range" wire:model.live="preferredAgeMin" min="18" max="80" step="1">
                </label>
                <label>
                    <span>Maximum age</span>
                    <input type="range" wire:model.live="preferredAgeMax" min="18" max="80" step="1">
                </label>
            </div>
        </section>

        <section class="filter-sheet__group" aria-labelledby="filter-intent-title">
            <h3 id="filter-intent-title">Relationship intent</h3>
            <div class="filter-sheet__choice-grid filter-sheet__choice-grid--intent">
                @foreach ($intentOptions as $value => $label)
                    <label class="filter-sheet__choice">
                        <input type="checkbox" wire:model="relationshipIntents" value="{{ $value }}">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="filter-sheet__group" aria-labelledby="filter-personality-title">
            <h3 id="filter-personality-title">Personality preference</h3>
            <div class="filter-sheet__choice-grid filter-sheet__choice-grid--personality">
                <span class="filter-sheet__disabled-choice filter-sheet__disabled-choice--selected">Any</span>
                <span class="filter-sheet__disabled-choice" aria-disabled="true">High compatibility</span>
            </div>
        </section>

        <button class="button button--primary filter-sheet__submit" type="submit" wire:loading.attr="disabled" wire:target="showMatches">
            <span aria-hidden="true">♡</span>
            <span wire:loading.remove wire:target="showMatches">Show matches</span>
            <span wire:loading wire:target="showMatches">Updating...</span>
        </button>
    </form>
</dialog>
