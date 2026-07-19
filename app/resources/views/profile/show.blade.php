@extends('layouts.public')

@section('title', $profile->display_name)

@section('content')
    <section class="panel profile-detail">
        @php($primaryPhoto = $profile->photos->where('is_primary', true)->sortBy('sort_order')->first() ?? $profile->photos->sortBy('sort_order')->first())
        @if ($primaryPhoto)
            <img class="profile-preview-photo" src="{{ $primaryPhoto->publicUrl() }}" alt="{{ $profile->display_name }} profile photo">
        @endif

        <div>
            <p class="eyebrow">Profil</p>
            <h1>{{ $profile->display_name }}</h1>
            <p>{{ $profile->birthdate->age }} • {{ $profile->city }}, {{ $profile->country_code }}</p>
            <p>{{ $profile->relationship_intent }}</p>
            <p>{{ $profile->bio }}</p>
            @php($visibleTags = $profile->tags->filter(fn ($tag) => in_array($tag->pivot->visibility, ['public', 'matches_only'], true))->groupBy(fn ($tag) => $tag->category?->label ?? 'Other'))
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
