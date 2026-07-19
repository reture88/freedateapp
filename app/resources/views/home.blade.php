@extends('layouts.public')

@section('title', 'Webshop alap')

@section('content')
    <section class="page-header">
        <div>
            <p class="eyebrow">Laravel CQRS webshop</p>
            <h1>Webapp Automation</h1>
            <p>Blade alapu, domainenkent bovitheto webshop alap.</p>
        </div>
    </section>

    <section class="panel">
        <h2>Rendszerallapot</h2>
        <dl class="status-list">
            <div>
                <dt>Alkalmazas</dt>
                <dd>{{ config('app.name') }}</dd>
            </div>
            <div>
                <dt>Architektura</dt>
                <dd>Laravel CQRS</dd>
            </div>
            <div>
                <dt>Bejelentkezes</dt>
                <dd>{{ auth()->check() ? 'aktiv' : 'vendeg' }}</dd>
            </div>
            @auth
                <div>
                    <dt>User</dt>
                    <dd>{{ auth()->user()->email }}</dd>
                </div>
            @endauth
        </dl>
    </section>
@endsection
