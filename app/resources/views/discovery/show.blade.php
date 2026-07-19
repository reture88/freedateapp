@extends('layouts.public')

@section('title', 'Discovery')

@section('content')
    @if ($candidate)
        @include('shared.discovery.profile-card', ['candidate' => $candidate])
    @else
        @include('components.empty-state', [
            'title' => 'No profiles available right now.',
            'message' => 'Update your preferences or check back later.',
            'actionUrl' => route('dating-preferences.edit'),
            'actionLabel' => 'Edit preferences',
        ])
    @endif
@endsection
