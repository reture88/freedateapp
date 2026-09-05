<div class="discovery-livewire">
    @if ($candidateData)
        @php
            $sharedTagCount = count($candidateData['shared_tags']);
            $compatibilityScore = min(96, 62 + ($sharedTagCount * 8));
            $intentLabel = str_replace('_', ' ', $candidateData['relationship_intent']);
        @endphp

        <main class="discovery-workspace" aria-label="Profile discovery">
            <header class="discovery-workspace__header">
                <div>
                    <h1>Your best matches</h1>
                    <p>Recommended</p>
                </div>
                <div class="discovery-tabs" aria-label="Discovery filters">
                    <a class="discovery-tabs__item discovery-tabs__item--active" href="{{ route('discovery.show') }}">For You</a>
                    <a class="discovery-tabs__item" href="{{ route('dating-preferences.edit') }}">Nearby</a>
                    <a class="discovery-tabs__item" href="{{ route('dating-preferences.edit') }}">New</a>
                    <a class="discovery-tabs__item" href="{{ route('dating-preferences.edit') }}">Online</a>
                </div>
            </header>

            <article class="discovery-profile-card">
                @include('components.profile-card', [
                    'displayName' => sprintf('%s, %d', $candidateData['display_name'], $candidateData['age']),
                    'age' => null,
                    'city' => $candidateData['city'],
                    'countryCode' => $candidateData['country_code'],
                    'relationshipIntent' => $candidateData['relationship_intent'],
                    'bio' => $candidateData['bio'],
                    'primaryPhotoUrl' => $candidateData['primary_photo_url'],
                    'sharedTags' => $candidateData['shared_tags'],
                ])
            </article>

            <div class="discovery-primary-actions" aria-label="Profile actions">
                <button class="button button--pill button--secondary" type="button" wire:click="reject" wire:loading.attr="disabled" wire:target="accept,reject">
                    Pass <span class="sr-only">Reject</span>
                </button>
                <button class="button button--pill button--primary" type="button" wire:click="accept" wire:loading.attr="disabled" wire:target="accept,reject">
                    Like <span class="sr-only">Accept</span>
                </button>
            </div>
        </main>

        <aside class="discovery-rail discovery-rail--right" aria-label="Compatibility and profile context">
            <h1>Why you might click</h1>

            <section class="context-card context-card--score">
                <div class="context-card__metric">
                    <h2>Compatibility</h2>
                    <strong>{{ $compatibilityScore }}%</strong>
                </div>
                <p>Based on shared interests and profile preferences.</p>
            </section>

            <section class="context-card">
                <h2>Top signals</h2>
                <ul class="signal-list">
                    <li>{{ $sharedTagCount }} shared {{ \Illuminate\Support\Str::plural('interest', $sharedTagCount) }}</li>
                    <li>Looking for {{ $intentLabel }}</li>
                    <li>Located in {{ $candidateData['city'] }}</li>
                </ul>
            </section>

            <section class="context-card context-card--muted">
                <h2>Conversation starter</h2>
                <p>Ask {{ $candidateData['display_name'] }} about something from their profile.</p>
            </section>

            <section class="context-card">
                <h2>Profile context</h2>
                <p><strong>Recently active</strong></p>
                <p>Intentions are visible before matching.</p>
                <div class="context-card__forms">
                    <form method="POST" action="{{ route('reports.submit') }}">
                        @csrf
                        <input type="hidden" name="target_type" value="profile">
                        <input type="hidden" name="target_id" value="{{ $candidateData['id'] }}">
                        <input type="hidden" name="category" value="profile_concern">
                        @include('components.confirm-button', [
                            'label' => 'Report profile',
                            'message' => 'Submit a safety report for this profile?',
                            'class' => 'button button--secondary',
                        ])
                    </form>

                    <form method="POST" action="{{ route('blocks.submit') }}">
                        @csrf
                        <input type="hidden" name="blocked_user_id" value="{{ $candidateData['user_id'] }}">
                        <input type="hidden" name="reason_category" value="safety">
                        @include('components.confirm-button', [
                            'label' => 'Block user',
                            'message' => 'Block this user and hide any shared chat?',
                            'class' => 'button button--danger',
                        ])
                    </form>
                </div>
            </section>
        </aside>
    @else
        <main class="discovery-workspace discovery-workspace--empty">
            @include('components.empty-state', [
                'title' => 'No profiles available right now.',
                'message' => 'Update your preferences or check back later.',
                'actionUrl' => route('dating-preferences.edit'),
                'actionLabel' => 'Edit preferences',
            ])
        </main>

        <aside class="discovery-rail discovery-rail--right" aria-label="Compatibility and profile context">
            <h1>Why you might click</h1>
            <section class="context-card context-card--muted">
                <h2>Profile context</h2>
                <p>New compatibility details appear when another profile is available.</p>
            </section>
        </aside>
    @endif
</div>
