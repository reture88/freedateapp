@php
    $headingLevel = (int) ($headingLevel ?? 2);
@endphp

<section class="{{ $class ?? 'panel empty-state' }}">
    @if ($headingLevel === 1)
        <h1>{{ $title }}</h1>
    @elseif ($headingLevel === 3)
        <h3>{{ $title }}</h3>
    @else
        <h2>{{ $title }}</h2>
    @endif
    <p>{{ $message }}</p>
    @isset($actionUrl)
        <a class="button button--secondary" href="{{ $actionUrl }}">{{ $actionLabel }}</a>
    @endisset
</section>
