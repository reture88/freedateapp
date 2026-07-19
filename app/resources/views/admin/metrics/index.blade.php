@extends('layouts.admin')

@section('title', 'Metrics dashboard')

@section('content')
    <section class="page-header">
        <div>
            <p class="eyebrow">Admin</p>
            <h1>Metrics dashboard</h1>
            <p>Launch funnel, safety, matching, chat, and assistant counters.</p>
        </div>
    </section>

    <div class="panel table-panel">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($metrics as $label => $count)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
