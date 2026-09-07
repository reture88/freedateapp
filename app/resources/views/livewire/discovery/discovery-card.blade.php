<div class="discovery-livewire">
    @if ($candidateData)
        @php
            $sharedTagCount = count($candidateData['shared_tags']);
            $compatibilityScore = min(96, 62 + ($sharedTagCount * 8));
            $compatibilityWidth = max(0, min(100, $compatibilityScore));
            $intentLabel = str_replace('_', ' ', $candidateData['relationship_intent']);
            $sharedInterestSummary = $sharedTagCount > 0 ? implode(' · ', $candidateData['shared_tags']) : 'No shared interests yet';
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
                    'variant' => 'discovery',
                ])
            </article>

            <div class="discovery-primary-actions" aria-label="Profile actions">
                <button class="button button--pill button--icon discovery-action discovery-action--pass" type="button" wire:click="reject" wire:loading.attr="disabled" wire:target="accept,reject" aria-label="Pass">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Reject</span>
                </button>
                <button class="button button--pill discovery-action discovery-action--special" type="button" disabled aria-disabled="true">
                    <span aria-hidden="true">&#10022;</span>
                    Special
                </button>
                <button class="button button--pill button--primary discovery-action discovery-action--like" type="button" wire:click="accept" wire:loading.attr="disabled" wire:target="accept,reject">
                    <span aria-hidden="true">&#9825;</span>
                    Like <span class="sr-only">Accept</span>
                </button>
            </div>
        </main>

        <aside class="discovery-rail discovery-rail--right" aria-label="Compatibility and profile context">
            <h1>Why you might click</h1>

            <section class="context-card context-card--score">
                <div class="context-card__metric">
                    <strong>{{ $compatibilityScore }}%</strong>
                    <span>{{ $compatibilityScore }}% MATCH</span>
                    <button class="context-card__affordance" type="button" onclick="document.getElementById('compatibility-sheet')?.showModal()">
                        Why you might click &rarr;
                    </button>
                </div>
                <div class="compatibility-progress" aria-hidden="true">
                    <span style="width: {{ $compatibilityWidth }}%"></span>
                </div>
                <p>Based on shared interests and profile preferences.</p>
            </section>

            <section class="context-card context-card--signals">
                <h2 class="sr-only">Top signals</h2>
                <ul class="signal-list">
                    <li><span aria-hidden="true">&#10022;</span>{{ $sharedTagCount }} shared {{ \Illuminate\Support\Str::plural('interest', $sharedTagCount) }}</li>
                    <li><span aria-hidden="true">&#10022;</span>Looking for {{ $intentLabel }}</li>
                    <li><span aria-hidden="true">&#10022;</span>Located in {{ $candidateData['city'] }}</li>
                </ul>
            </section>

            <section class="context-card context-card--muted">
                <h2>Conversation starter</h2>
                <p>Ask {{ $candidateData['display_name'] }} about something from their profile.</p>
                <button class="button button--compact button--primary context-card__disabled-action" type="button" disabled aria-disabled="true">Use opener</button>
            </section>

            <section class="context-card context-card--profile-context">
                <h2>Profile context</h2>
                <dl class="profile-context-list">
                    <div>
                        <dt>Activity</dt>
                        <dd>Recently active</dd>
                    </div>
                    <div>
                        <dt>Intent</dt>
                        <dd>{{ ucfirst($intentLabel) }}</dd>
                    </div>
                    <div>
                        <dt>Personality</dt>
                        <dd>{{ $sharedTagCount }} shared {{ \Illuminate\Support\Str::plural('interest', $sharedTagCount) }}</dd>
                    </div>
                </dl>
            </section>

            <div class="context-card__forms" aria-label="Safety actions">
                <form method="POST" action="{{ route('reports.submit') }}">
                    @csrf
                    <input type="hidden" name="target_type" value="profile">
                    <input type="hidden" name="target_id" value="{{ $candidateData['id'] }}">
                    <input type="hidden" name="category" value="profile_concern">
                    @include('components.confirm-button', [
                        'label' => 'Report',
                        'message' => 'Submit a safety report for this profile?',
                        'class' => 'button button--text',
                    ])
                </form>

                <form method="POST" action="{{ route('blocks.submit') }}">
                    @csrf
                    <input type="hidden" name="blocked_user_id" value="{{ $candidateData['user_id'] }}">
                    <input type="hidden" name="reason_category" value="safety">
                    @include('components.confirm-button', [
                        'label' => 'Block',
                        'message' => 'Block this user and hide any shared chat?',
                        'class' => 'button button--text',
                    ])
                </form>
            </div>
        </aside>

        <dialog class="compatibility-sheet" id="compatibility-sheet" aria-labelledby="compatibility-sheet-title" onclick="if (event.target === this) this.close()">
            <section class="compatibility-sheet__panel" aria-label="Compatibility details">
                <div class="compatibility-sheet__handle" aria-hidden="true"></div>
                <header class="compatibility-sheet__header">
                    <h2 id="compatibility-sheet-title">Why you might click</h2>
                    <button class="compatibility-sheet__close" type="button" onclick="document.getElementById('compatibility-sheet')?.close()" aria-label="Close compatibility details">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </header>

                <section class="compatibility-sheet__score" aria-label="Compatibility score">
                    <div class="compatibility-sheet__score-row">
                        <strong>{{ $compatibilityScore }}%</strong>
                        <span>{{ $compatibilityScore }}% MATCH</span>
                    </div>
                    <div class="compatibility-progress" aria-hidden="true">
                        <span style="width: {{ $compatibilityWidth }}%"></span>
                    </div>
                    <p>Shared values, communication style and interests.</p>
                </section>

                <section class="compatibility-sheet__signals" aria-label="Compatibility signals">
                    <ul class="signal-list">
                        <li><span aria-hidden="true">&#10022;</span>{{ $sharedTagCount }} shared {{ \Illuminate\Support\Str::plural('interest', $sharedTagCount) }}</li>
                        <li><span aria-hidden="true">&#10022;</span>Looking for {{ $intentLabel }}</li>
                        <li><span aria-hidden="true">&#10022;</span>Located in {{ $candidateData['city'] }}</li>
                    </ul>
                </section>

                <section class="compatibility-sheet__context" aria-label="Profile compatibility context">
                    <dl>
                        <div>
                            <dt>Personality</dt>
                            <dd class="compatibility-sheet__muted-value">Not available yet</dd>
                        </div>
                        <div>
                            <dt>Intent</dt>
                            <dd>{{ ucfirst($intentLabel) }}</dd>
                        </div>
                        <div>
                            <dt>Shared interests</dt>
                            <dd>{{ $sharedInterestSummary }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="compatibility-sheet__starter">
                    <h2>Conversation starter</h2>
                    <p>Ask {{ $candidateData['display_name'] }} about something from their profile.</p>
                    <button class="button compatibility-sheet__opener" type="button" disabled aria-disabled="true">Use opener</button>
                </section>
            </section>
        </dialog>
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
