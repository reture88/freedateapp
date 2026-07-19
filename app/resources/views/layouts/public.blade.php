@extends('layouts.base')

@section('body_class', 'app-shell public-shell')

@section('body')
    <header class="topbar">
        <a href="{{ route('home') }}" class="brand">Freedateapp</a>
        <nav class="topbar__nav" aria-label="Fo navigacio">
            <a href="{{ route('home') }}">Kezdolap</a>
            @auth
                <a href="{{ route('profile.show') }}">Profil</a>
                @if (auth()->user()?->canAccessAdminPanel())
                    <a href="{{ route('admin.users.index') }}">Admin</a>
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
    </header>

    <main class="page">
        @include('shared.flash')
        @yield('content')
    </main>
@endsection
