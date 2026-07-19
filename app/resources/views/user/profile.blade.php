@extends('layouts.public')

@section('title', 'Profil')

@section('content')
    <section class="page-header">
        <div>
            <p class="eyebrow">Fiok</p>
            <h1>Profil</h1>
            <p>Alapadatok, jelszo es rendeléshez hasznalt cimek kezelese.</p>
        </div>
    </section>

    <div class="grid grid--two">
        <section class="panel">
            <h2>Alapadatok</h2>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @include('shared.form-errors')

                <label for="name">Nev</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>

                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>

                <button type="submit" class="button button--primary button--inline">Mentes</button>
            </form>
        </section>

        <section class="panel">
            <h2>Jelszo modositasa</h2>
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf

                <label for="current_password">Jelenlegi jelszo</label>
                <input id="current_password" name="current_password" type="password" required>

                <label for="password">Uj jelszo</label>
                <input id="password" name="password" type="password" required>

                <label for="password_confirmation">Uj jelszo megerositese</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>

                <button type="submit" class="button button--primary button--inline">Jelszo mentese</button>
            </form>
        </section>
    </div>

    <div class="grid grid--two grid--spaced">
        @include('user.partials.address-form', [
            'title' => 'Szallitasi cim',
            'type' => 'shipping',
            'address' => $shippingAddress,
        ])

        @include('user.partials.address-form', [
            'title' => 'Szamlazasi cim',
            'type' => 'billing',
            'address' => $billingAddress,
        ])
    </div>
@endsection
