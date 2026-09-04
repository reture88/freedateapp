@extends('layouts.public')

@section('title', 'Discovery')

@section('content')
    <livewire:discovery.discovery-card :candidate="$candidate" />
@endsection
