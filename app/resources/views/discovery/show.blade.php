@extends('layouts.public')

@section('title', 'Discovery')
@section('body_class', 'app-shell public-shell public-shell--discovery')
@section('brand_label', 'CONNECT')

@section('topbar_nav')
    <nav class="topbar__nav topbar__nav--discovery" aria-label="Discovery actions">
        <a class="button button--secondary button--compact" href="{{ route('dating-preferences.edit') }}">Filters</a>
        <a class="button button--secondary button--compact" href="{{ route('dating-profile.edit') }}">Profile</a>
    </nav>
@endsection

@section('content')
    <section class="discovery-dashboard" aria-label="Discovery dashboard">
        <aside class="discovery-rail discovery-rail--left" aria-label="Discovery navigation and matches">
            <div class="discovery-rail__section">
                <h1>Discover</h1>
                <nav class="discovery-nav" aria-label="Discovery views">
                    <a class="discovery-nav__item discovery-nav__item--active" href="{{ route('discovery.show') }}">For You</a>
                    <a class="discovery-nav__item" href="{{ route('dating-preferences.edit') }}">People Nearby</a>
                    <a class="discovery-nav__item" href="{{ route('dating-preferences.edit') }}">Personality Match</a>
                    <a class="discovery-nav__item" href="{{ route('matches.index') }}">Communities</a>
                </nav>
            </div>

            <div class="discovery-rail__section discovery-rail__section--matches">
                <div class="discovery-rail__heading">
                    <h2>Matches</h2>
                    <span>{{ count($matches) }}</span>
                </div>

                <div class="discovery-match-list">
                    @forelse (array_slice($matches, 0, 4) as $match)
                        <a class="discovery-match-list__item" href="{{ route('conversations.show', ['conversation' => $match->conversationId]) }}">
                            <span class="discovery-match-list__avatar" aria-hidden="true">{{ strtoupper(substr($match->displayName, 0, 1)) }}</span>
                            <span>
                                <strong>{{ $match->displayName }}</strong>
                                <small>{{ $match->city }}</small>
                            </span>
                        </a>
                    @empty
                        <p class="discovery-rail__muted">No matches yet.</p>
                    @endforelse
                </div>
            </div>

            <section class="discovery-teaser" aria-label="Discovery tip">
                <h2>See who likes you</h2>
                <p>Refine your preferences and keep discovery focused.</p>
            </section>
        </aside>

        <livewire:discovery.discovery-card :candidate="$candidate" />
    </section>
@endsection
