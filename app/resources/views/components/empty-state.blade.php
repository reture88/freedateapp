<section class="panel empty-state">
    <h1>{{ $title }}</h1>
    <p>{{ $message }}</p>
    @isset($actionUrl)
        <a href="{{ $actionUrl }}">{{ $actionLabel }}</a>
    @endisset
</section>
