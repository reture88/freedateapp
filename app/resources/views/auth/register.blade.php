@extends('layouts.auth')

@section('title', 'Regisztracio')

@section('content')
    <form method="POST" action="{{ route('auth.register') }}" class="form-card">
        @csrf

        <div class="form-card__header">
            <h1>Regisztracio</h1>
            <p>Hozz letre egy vasarloi fiokot a rendelesekhez.</p>
        </div>

        @include('shared.form-errors')

        <label for="name">Nev</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="password">Jelszo</label>
        <input id="password" name="password" type="password" required>

        <label for="password_confirmation">Jelszo megerositese</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <button type="submit" class="button button--primary">Regisztracio</button>

        <p class="form-card__footer">
            Mar van fiokod?
            <a href="{{ route('auth.login.show-form') }}">Belepes</a>
        </p>
    </form>
@endsection
