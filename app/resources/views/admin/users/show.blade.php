@extends('layouts.admin')

@section('title', 'Felhasznalo')

@section('content')
    <section class="page-header">
        <div>
            <p class="eyebrow">Admin</p>
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->email }}</p>
        </div>
    </section>

    @include('shared.form-errors')

    <div class="grid grid--two">
        <section class="panel">
            <h2>Adatok</h2>
            <dl class="summary-list">
                <div><dt>Cimek</dt><dd>{{ $user->addresses_count }}</dd></div>
                <div><dt>Aktiv</dt><dd>{{ $user->is_active ? 'Igen' : 'Nem' }}</dd></div>
            </dl>
        </section>

        <section class="panel">
            <h2>Jogosultsag es allapot</h2>
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                <label for="role">Szerepkor</label>
                <select id="role" name="role">
                    <option value="customer" @selected($user->role === 'customer')>customer</option>
                    <option value="admin" @selected($user->role === 'admin')>admin</option>
                </select>

                <label class="checkbox-line">
                    <input type="checkbox" name="is_active" value="1" @checked($user->is_active)>
                    Aktiv felhasznalo
                </label>

                <button type="submit" class="button button--primary button--inline">Mentes</button>
            </form>

            <form method="POST" action="{{ route('admin.users.moderation', $user) }}">
                @csrf
                <input type="hidden" name="action" value="suspend">
                @include('components.confirm-button', [
                    'label' => 'Suspend',
                    'message' => 'Suspend this user account?',
                ])
            </form>
            <form method="POST" action="{{ route('admin.users.moderation', $user) }}">
                @csrf
                <input type="hidden" name="action" value="restore">
                <button type="submit">Restore</button>
            </form>
        </section>
    </div>
@endsection
