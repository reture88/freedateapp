@extends('layouts.public')

@section('title', $profile ? 'Edit dating profile' : 'Dating profile onboarding')

@section('content')
    <livewire:profile.dating-profile-form />
@endsection
