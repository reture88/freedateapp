@extends('layouts.base')

@section('title', 'MTAM')
@section('body_class', 'mtam-auth-shell mtam-landing-shell')

@section('body')
    <main class="mtam-landing">
        <header class="mtam-landing__top">
            @include('auth.partials.brand')
            <nav class="mtam-landing__nav" aria-label="Account actions">
                <a class="mtam-auth-button mtam-auth-button--secondary" href="{{ route('auth.login.show-form') }}">Log in</a>
                <a class="mtam-auth-button mtam-auth-button--primary" href="{{ route('auth.register.show-form') }}">Sign up</a>
            </nav>
        </header>

        <section class="mtam-landing__hero">
            <div class="mtam-landing__copy">
                <h1>More than a match.</h1>
                <p>Meet people with more context, personality, compatibility and the little things that make a conversation worth starting.</p>
                <div class="mtam-landing__actions">
                    <a class="mtam-auth-button mtam-auth-button--primary" href="{{ route('auth.register.show-form') }}">Sign up</a>
                    <a class="mtam-auth-button mtam-auth-button--secondary" href="{{ route('auth.login.show-form') }}">Log in</a>
                </div>
                <span class="mtam-landing__eyebrow">LESS SWIPE-FIRST. MORE TO GO ON.</span>
            </div>

            <div class="mtam-landing__preview" aria-hidden="true">
                <div class="mtam-landing__accent"></div>
                <article class="mtam-preview-card">
                    <div class="mtam-preview-card__media">
                        <div class="mtam-preview-card__face"></div>
                        <div class="mtam-preview-card__identity">
                            <strong>Anna, 29</strong>
                            <span>Product designer - Budapest - 2 km</span>
                        </div>
                    </div>
                    <div class="mtam-preview-card__body">
                        <p>Weekend trips, good coffee, long conversations and someone curious about the world.</p>
                        <div class="mtam-preview-card__chips">
                            <span class="mtam-chip mtam-chip--personality">ENFP</span>
                            <span class="mtam-chip">Travel</span>
                            <span class="mtam-chip">Cats</span>
                            <span class="mtam-chip mtam-chip--intent">Relationship</span>
                        </div>
                    </div>
                </article>
                <aside class="mtam-compat-preview">
                    <div>
                        <strong>86% MATCH</strong>
                        <span>Why you might click -></span>
                    </div>
                    <span class="mtam-compat-preview__bar"><i></i></span>
                    <small>Similar social energy</small>
                    <small>4 shared interests</small>
                </aside>
            </div>
        </section>

        <section class="mtam-landing__values" aria-label="MTAM values">
            <article>
                <span></span>
                <strong>Personality matters</strong>
                <p>See more than a photo before you decide.</p>
            </article>
            <article>
                <span class="mtam-landing__dot-purple"></span>
                <strong>Know why you might click</strong>
                <p>Compatibility is explained, not scored like analytics.</p>
            </article>
            <article>
                <span></span>
                <strong>Start better conversations</strong>
                <p>Shared context gives you somewhere real to begin.</p>
            </article>
        </section>
    </main>
@endsection
