@php
    $displayName = $displayName ?? 'Profile';
    $headingLevel = (int) ($headingLevel ?? 1);
    $variant = $variant ?? 'default';
    $relationshipIntent = $relationshipIntent ?? null;
    $metaParts = array_filter([
        $age ?? null,
        trim(implode(', ', array_filter([$city ?? null, $countryCode ?? null]))),
    ], fn ($part) => filled($part));
    $sharedTags = $sharedTags ?? [];
    $cardClass = 'profile-card'.($variant === 'discovery' ? ' profile-card--discovery' : '');
    $intentLabel = $relationshipIntent ? str_replace('_', ' ', $relationshipIntent) : null;
@endphp

<section class="{{ $cardClass }}">
    <div class="profile-card__media">
        @if (!empty($primaryPhotoUrl))
            <img class="profile-card__photo" src="{{ $primaryPhotoUrl }}" alt="{{ $displayName }} profile photo">
        @else
            <div class="profile-card__placeholder" aria-hidden="true">
                {{ strtoupper(substr($displayName, 0, 1)) }}
            </div>
        @endif

        @if ($variant === 'discovery')
            <div class="profile-card__gallery-dots" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="profile-card__identity">
                <h2 class="profile-card__title">{{ $displayName }}</h2>
                @if ($metaParts !== [])
                    <p class="profile-card__meta">{{ implode(' · ', $metaParts) }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="profile-card__body">
        @if ($variant !== 'discovery')
            @if ($headingLevel === 1)
                <h1 class="profile-card__title">{{ $displayName }}</h1>
            @elseif ($headingLevel === 3)
                <h3 class="profile-card__title">{{ $displayName }}</h3>
            @else
                <h2 class="profile-card__title">{{ $displayName }}</h2>
            @endif

            @if ($metaParts !== [])
                <p class="profile-card__meta">{{ implode(' • ', $metaParts) }}</p>
            @endif
        @endif

        @if (!empty($relationshipIntent) && $variant !== 'discovery')
            <p class="profile-card__intent">
                @include('components.status-badge', [
                    'status' => $relationshipIntent,
                    'variant' => 'intent',
                ])
            </p>
        @endif

        @if (!empty($bio))
            <p class="profile-card__bio">{{ $bio }}</p>
        @endif

        @if ($sharedTags !== [])
            <section class="shared-interest-list" aria-label="Shared interests">
                <h2>Shared interests</h2>
                <div class="tag-list">
                    @if ($variant === 'discovery')
                        <span class="tag-list__personality">Shared</span>
                    @endif
                    @foreach ($sharedTags as $tag)
                        <span>{{ $tag }}</span>
                    @endforeach
                    @if ($intentLabel && $variant === 'discovery')
                        <span class="tag-list__intent">{{ ucfirst($intentLabel) }}</span>
                    @endif
                </div>
            </section>
        @elseif ($intentLabel && $variant === 'discovery')
            <section class="shared-interest-list" aria-label="Profile tags">
                <h2>Profile tags</h2>
                <div class="tag-list">
                    <span class="tag-list__intent">{{ ucfirst($intentLabel) }}</span>
                </div>
            </section>
        @endif

        @if ($intentLabel && $variant === 'discovery')
            <p class="profile-card__intent-note">Looking for {{ $intentLabel }}</p>
        @endif
    </div>
</section>
