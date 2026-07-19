@extends('layouts.admin')

@section('title', 'Report queue')

@section('content')
    <section class="page-header">
        <div>
            <p class="eyebrow">Admin</p>
            <h1>Report queue</h1>
            <p>Open safety reports waiting for moderation.</p>
        </div>
    </section>

    <div class="panel table-panel">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Target</th>
                    <th>Reporter</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td>{{ $report->category }}</td>
                        <td>{{ $report->target_type }} #{{ $report->target_id }}</td>
                        <td>{{ $report->reporter?->email }}</td>
                        <td><a href="{{ route('admin.reports.show', $report) }}">Details</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4">No open reports.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('shared.pagination', ['paginator' => $reports])
@endsection
