@extends('layouts.base')

@section('body_class', 'auth-shell')

@section('body')
    <main class="auth-page">
        @include('shared.flash')
        @yield('content')
    </main>
@endsection
