@extends('layouts.public')

@section('title', $profile->display_name)

@section('content')
    <section class="panel profile-detail">
        @php($primaryPhoto = $profile->photos->where('is_primary', true)->sortBy('sort_order')->first() ?? $profile->photos->sortBy('sort_order')->first())
        @php($visibleTags = $profile->tags->filter(fn ($tag) => in_array($tag->pivot->visibility, ['public', 'matches_only'], true))->groupBy(fn ($tag) => $tag->category?->label ?? 'Other'))

        <div>
            <p class="eyebrow">Profil</p>
            @include('components.profile-card', [
                'displayName' => $profile->display_name,
                'age' => $profile->birthdate->age,
                'city' => $profile->city,
                'countryCode' => $profile->country_code,
                'relationshipIntent' => $profile->relationship_intent,
                'bio' => $profile->bio,
                'primaryPhotoUrl' => $primaryPhoto?->publicUrl(),
                'sharedTags' => [],
            ])

            @if ($visibleTags->isNotEmpty())
                <div class="profile-tag-display">
                    @foreach ($visibleTags as $categoryLabel => $tags)
                        <section>
                            <h2>{{ $categoryLabel }}</h2>
                            <div class="tag-list">
                                @foreach ($tags as $tag)
                                    <span>{{ $tag->label }}</span>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
