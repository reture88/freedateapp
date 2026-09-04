@extends('layouts.public')

@section('title', 'Dating preferences')

@section('content')
    <livewire:preference.edit-preference-form :preference="$preference" :summary="$summary" />
@endsection
