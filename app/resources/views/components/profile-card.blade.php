@php
    $displayName = $displayName ?? 'Profile';
    $headingLevel = (int) ($headingLevel ?? 1);
    $metaParts = array_filter([
        $age ?? null,
        trim(implode(', ', array_filter([$city ?? null, $countryCode ?? null]))),
    ], fn ($part) => filled($part));
    $sharedTags = $sharedTags ?? [];
@endphp

<section class="profile-card">
    @if (!empty($primaryPhotoUrl))
        <img class="profile-card__photo" src="{{ $primaryPhotoUrl }}" alt="{{ $displayName }} profile photo">
    @else
        <div class="profile-card__placeholder" aria-hidden="true">
            {{ strtoupper(substr($displayName, 0, 1)) }}
        </div>
    @endif

    <div class="profile-card__body">
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

        @if (!empty($relationshipIntent))
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
                    @foreach ($sharedTags as $tag)
                        <span>{{ $tag }}</span>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</section>
