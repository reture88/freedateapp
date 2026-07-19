@extends('layouts.public')

@section('title', 'Discovery')

@section('content')
    @if ($candidate)
        @include('shared.discovery.profile-card', ['candidate' => $candidate])
    @else
        <section class="panel">
            <h1>No profiles available right now.</h1>
            <p>Update your preferences or check back later.</p>
        </section>
    @endif
@endsection
