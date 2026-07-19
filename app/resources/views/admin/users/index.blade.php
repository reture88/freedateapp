@extends('layouts.admin')

@section('title', 'Felhasznalok')

@section('content')
    <section class="page-header">
        <div>
            <p class="eyebrow">Admin</p>
            <h1>Felhasznalok</h1>
            <p>Felhasznalok listazasa, szerepkor es aktiv allapot szerinti szurese.</p>
        </div>
    </section>

    <form method="GET" action="{{ route('admin.users.index') }}" class="panel admin-filter">
        <div>
            <label for="q">Kereses</label>
            <input id="q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Nev vagy email">
        </div>
        <div>
            <label for="role">Szerepkor</label>
            <select id="role" name="role">
                <option value="">Osszes</option>
                <option value="customer" @selected(($filters['role'] ?? '') === 'customer')>customer</option>
                <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>admin</option>
            </select>
        </div>
        <div>
            <label for="is_active">Aktivitas</label>
            <select id="is_active" name="is_active">
                <option value="">Osszes</option>
                <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Aktiv</option>
                <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inaktiv</option>
            </select>
        </div>
        <button type="submit" class="button button--primary button--inline">Szures</button>
    </form>

    <div class="panel table-panel">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nev</th>
                    <th>Email</th>
                    <th>Szerepkor</th>
                    <th>Aktiv</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->is_active ? 'Igen' : 'Nem' }}</td>
                        <td><a href="{{ route('admin.users.show', $user) }}">Reszletek</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">Nincs felhasznalo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('shared.pagination', ['paginator' => $users])
@endsection
