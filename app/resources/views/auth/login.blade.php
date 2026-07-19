@extends('layouts.auth')

@section('title', 'Bejelentkezes')

@section('content')
    <form method="POST" action="{{ route('auth.login') }}" class="form-card">
        @csrf

        <div class="form-card__header">
            <h1>Bejelentkezes</h1>
            <p>Admin vagy vasarloi fiok hasznalataval tudsz tovabblepni.</p>
        </div>

        @include('shared.form-errors')

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Jelszo</label>
        <input id="password" name="password" type="password" required>

        <label class="checkbox-line">
            <input name="remember" type="checkbox" value="1" @checked(old('remember'))>
            Emlekezz ram
        </label>

        <button type="submit" class="button button--primary">Belepes</button>

        <p class="form-card__footer">
            Nincs meg fiokod?
            <a href="{{ route('auth.register.show-form') }}">Regisztracio</a>
        </p>
    </form>
@endsection
