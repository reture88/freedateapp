@extends('layouts.admin')

@section('title', 'Report detail')

@section('content')
    <section class="page-header">
        <div>
            <p class="eyebrow">Admin</p>
            <h1>Report #{{ $report->getKey() }}</h1>
            <p>{{ $report->status }}</p>
        </div>
    </section>

    <section class="panel">
        <dl class="summary-list">
            <div><dt>Category</dt><dd>{{ $report->category }}</dd></div>
            <div><dt>Target</dt><dd>{{ $report->target_type }} #{{ $report->target_id }}</dd></div>
            <div><dt>Reporter</dt><dd>{{ $report->reporter?->email }}</dd></div>
            <div><dt>Note</dt><dd>{{ $report->note ?? 'No note.' }}</dd></div>
        </dl>
    </section>
@endsection
