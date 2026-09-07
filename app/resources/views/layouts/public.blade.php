@extends('layouts.base')

@section('body_class', 'app-shell public-shell')

@section('body')
    <header class="topbar">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand__mark">@yield('brand_label', 'Freedateapp')</span>
            @hasSection('brand_detail')
                <span class="brand__detail">@yield('brand_detail')</span>
            @endif
        </a>
        @hasSection('topbar_nav')
            @yield('topbar_nav')
        @else
            <nav class="topbar__nav" aria-label="Fo navigacio">
                <a href="{{ route('home') }}">Kezdolap</a>
                @auth
                    <a href="{{ route('profile.show') }}">Profil</a>
                    <a href="{{ route('dating-profile.edit') }}">Tarskereso profil</a>
                    <a href="{{ route('discovery.show') }}">Discovery</a>
                    <a href="{{ route('matches.index') }}">Matchek</a>
                    <a href="{{ route('dating-preferences.edit') }}">Preferenciak</a>
                    @if (auth()->user()?->canAccessAdminPanel())
                        <a href="{{ url('/admin-panel') }}">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="link-button">Kilepes</button>
                    </form>
                @else
                    <a href="{{ route('auth.login.show-form') }}">Belepes</a>
                    <a href="{{ route('auth.register.show-form') }}">Regisztracio</a>
                @endauth
            </nav>
        @endif
    </header>

    <main class="page">
        @include('shared.flash')
        @yield('content')
    </main>
@endsection
