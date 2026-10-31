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
                <div><dt>Fiokallapot</dt><dd>{{ $user->accountStatus()->label() }}</dd></div>
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

                <label for="account_status">Fiokallapot</label>
                <select id="account_status" name="account_status">
                    @foreach (\App\User\Enum\AccountStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($user->accountStatus() === $status)>{{ $status->label() }}</option>
                    @endforeach
                </select>

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
                <input type="hidden" name="action" value="ban">
                @include('components.confirm-button', [
                    'label' => 'Ban',
                    'message' => 'Ban this user account?',
                ])
            </form>
            <form method="POST" action="{{ route('admin.users.moderation', $user) }}">
                @csrf
                <input type="hidden" name="action" value="deactivate">
                @include('components.confirm-button', [
                    'label' => 'Deactivate',
                    'message' => 'Deactivate this user account?',
                ])
            </form>
            <form method="POST" action="{{ route('admin.users.moderation', $user) }}">
                @csrf
                <input type="hidden" name="action" value="restore">
                <button class="button button--secondary button--inline" type="submit">Restore</button>
            </form>
        </section>
    </div>
@endsection
