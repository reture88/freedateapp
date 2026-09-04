@php
    $label = $label ?? \Illuminate\Support\Str::of((string) $status)->replace('_', ' ')->title();
    $variant = preg_replace('/[^a-z0-9_-]+/', '-', strtolower((string) ($variant ?? $status)));
@endphp

<span class="status-badge status-badge--{{ $variant }}">{{ $label }}</span>
