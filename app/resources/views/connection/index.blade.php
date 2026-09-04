@extends('layouts.public')

@section('title', 'Matches')

@section('content')
    <livewire:connection.match-list :matches="$matches" />
@endsection
