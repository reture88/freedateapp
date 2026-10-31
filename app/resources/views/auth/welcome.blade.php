@extends('layouts.auth')

@section('title', 'Start your profile')

@section('content')
    <section class="mtam-welcome">
        <header class="mtam-auth-flow__top">
            @include('auth.partials.brand')
        </header>

        <div class="mtam-welcome__grid">
            <div class="mtam-welcome__copy">
                <span class="mtam-welcome__badge">Account created</span>
                <h1>Now let's build your profile.</h1>
                <p>Tell us a little more about you: personality, interests and what you're looking for. This is where MTAM becomes more than a login.</p>
                <a class="mtam-auth-button mtam-auth-button--primary" href="{{ route('dating-profile.onboarding.show') }}">Set up my profile</a>
                <small>Next: Dating Profile Onboarding</small>
            </div>

            <div class="mtam-landing__preview mtam-welcome__preview" aria-hidden="true">
                <div class="mtam-landing__accent"></div>
                <article class="mtam-preview-card">
                    <div class="mtam-preview-card__media">
                        <div class="mtam-preview-card__face"></div>
                        <div class="mtam-preview-card__identity">
                            <strong>Your profile</strong>
                            <span>Personality - interests - intent</span>
                        </div>
                    </div>
                    <div class="mtam-preview-card__body">
                        <p>Your profile gives matches something meaningful to respond to.</p>
                    </div>
                </article>
                <aside class="mtam-compat-preview">
                    <div>
                        <strong>Compatibility</strong>
                        <span>Context first</span>
                    </div>
                    <span class="mtam-compat-preview__bar"><i></i></span>
                    <small>Signals appear after setup</small>
                </aside>
            </div>
        </div>
    </section>
@endsection
