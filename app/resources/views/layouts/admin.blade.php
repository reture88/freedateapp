@extends('layouts.base')

@section('body_class', 'app-shell admin-shell')

@section('body')
    <header class="topbar topbar--admin">
        <a href="{{ route('home') }}" class="brand">Admin</a>
        <nav class="topbar__nav" aria-label="Admin navigacio">
            <a href="{{ route('home') }}">Public oldal</a>
            <a href="{{ route('admin.users.index') }}">Felhasznalok</a>
            <a href="{{ route('admin.reports.index') }}">Reportok</a>
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button type="submit" class="link-button">Kilepes</button>
            </form>
        </nav>
    </header>

    <main class="page page--admin">
        @include('shared.flash')
        @yield('content')
    </main>
@endsection
