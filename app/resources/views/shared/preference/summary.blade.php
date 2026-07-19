<p>{{ $summary->displayText }}</p>
@if ($summary->hardFilters['exclude_ai_wingman_enabled'])
    <p>AI Szarnyseged-enabled profiles excluded</p>
@endif
