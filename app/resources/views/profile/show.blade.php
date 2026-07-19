@extends('layouts.public')

@section('title', $profile->display_name)

@section('content')
    <section class="panel profile-detail">
        @php($primaryPhoto = $profile->photos->where('is_primary', true)->sortBy('sort_order')->first() ?? $profile->photos->sortBy('sort_order')->first())
        @if ($primaryPhoto)
            <img class="profile-preview-photo" src="{{ $primaryPhoto->publicUrl() }}" alt="{{ $profile->display_name }} profile photo">
        @endif

        <div>
            <p class="eyebrow">Profil</p>
            <h1>{{ $profile->display_name }}</h1>
            <p>{{ $profile->birthdate->age }} • {{ $profile->city }}, {{ $profile->country_code }}</p>
            <p>{{ $profile->relationship_intent }}</p>
            <p>{{ $profile->bio }}</p>
        </div>
    </section>
@endsection
