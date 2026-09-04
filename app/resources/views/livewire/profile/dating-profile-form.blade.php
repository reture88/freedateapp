<section class="panel">
    <h1>{{ $profileId ? 'Edit dating profile' : 'Dating profile onboarding' }}</h1>
    <p>Create the profile other people will see in discovery.</p>

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
        <label>
            Display name
            <input wire:model="displayName" required maxlength="80">
        </label>

        <label>
            Birthdate
            <input type="date" wire:model="birthdate" required>
        </label>

        <label>
            Gender
            <select wire:model="gender" required>
                @foreach ($genderOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Orientation
            <select wire:model="orientation" required>
                @foreach ($orientationOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>
            City
            <input wire:model="city" required maxlength="120">
        </label>

        <label>
            Country code
            <input wire:model="countryCode" required maxlength="2">
            <span class="field-help">Use a two-letter ISO country code, for example HU or GB.</span>
        </label>

        <label>
            Approximate latitude
            <input wire:model="approximateLatitude">
            <span class="field-help">Optional. Used only for distance-based discovery.</span>
        </label>

        <label>
            Approximate longitude
            <input wire:model="approximateLongitude">
            <span class="field-help">Optional. Pair it with latitude to improve nearby matches.</span>
        </label>

        <label>
            Relationship intent
            <select wire:model="relationshipIntent" required>
                @foreach ($intentOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Bio
            <textarea wire:model="bio" maxlength="1000"></textarea>
            <span class="field-help">Keep it practical: what you like, what you are looking for, and an easy conversation hook.</span>
        </label>

        <label class="checkbox-row">
            <input type="checkbox" wire:model="aiWingmanEnabled">
            <span>
                Enable AI Szarnyseged playful starter messages
                <small>When both matched people stay silent, the assistant can post one light opener in the chat.</small>
            </span>
        </label>

        <label class="checkbox-row">
            <input type="checkbox" wire:model="privacyConsent" required>
            <span>I understand my visible profile details can be used for matching and discovery.</span>
        </label>

        @if ($tagCategories !== [])
            <section class="profile-tag-picker">
                <h2>Profile interests</h2>
                <p class="muted">Choose public interests or keep some private for matching only.</p>

                @foreach ($tagCategories as $category)
                    <fieldset class="profile-tag-group" wire:key="tag-category-{{ $category['id'] }}">
                        <legend>{{ $category['label'] }}</legend>
                        <div class="profile-tag-options">
                            @foreach ($category['tags'] as $tag)
                                <label class="profile-tag-option" wire:key="profile-tag-{{ $tag['id'] }}">
                                    <span>
                                        <input type="checkbox" wire:model="selectedProfileTags" value="{{ $tag['id'] }}">
                                        {{ $tag['label'] }}
                                    </span>
                                    <select wire:model="profileTagVisibility.{{ $tag['id'] }}">
                                        <option value="public">Public</option>
                                        <option value="matches_only">Matches only</option>
                                        <option value="private_matching">Private matching</option>
                                    </select>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </section>
        @endif

        <button class="button button--primary" type="submit" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">Save dating profile</span>
            <span wire:loading wire:target="save">Saving...</span>
        </button>
    </form>

    @if ($profileId)
        <section class="profile-photo-manager">
            <h2>Profile photos</h2>
            <p class="muted">Upload up to 7 photos and choose which one appears first in discovery.</p>

            <form wire:submit.prevent="uploadPhoto" class="stack">
                <label>
                    Add photo
                    <input type="file" wire:model="photo" accept="image/*" required>
                </label>
                @error('photo')
                    <p class="field-error">{{ $message }}</p>
                @enderror
                <button type="submit" class="button button--secondary" wire:loading.attr="disabled" wire:target="photo,uploadPhoto">
                    <span wire:loading.remove wire:target="photo,uploadPhoto">Upload photo</span>
                    <span wire:loading wire:target="photo,uploadPhoto">Uploading...</span>
                </button>
            </form>

            @if ($photos !== [])
                <div class="profile-photo-grid">
                    @foreach ($photos as $profilePhoto)
                        <article class="profile-photo-card" wire:key="profile-photo-{{ $profilePhoto['id'] }}">
                            <img src="{{ $profilePhoto['url'] }}" alt="Profile photo">
                            <div class="profile-photo-card__footer">
                                @if ($profilePhoto['is_primary'])
                                    <span class="status-pill">Primary</span>
                                @else
                                    <button type="button" class="button button--secondary" wire:click="choosePrimary({{ $profilePhoto['id'] }})">
                                        Make primary
                                    </button>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</section>
