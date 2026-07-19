@php($primaryPhoto = $candidate->photos()->where('is_primary', true)->orderBy('sort_order')->first())

<article class="panel">
    @if ($primaryPhoto)
        <img src="{{ $primaryPhoto->path }}" alt="{{ $candidate->display_name }} profile photo">
    @endif
    <h1>{{ $candidate->display_name }}</h1>
    <p>{{ $candidate->birthdate->age }} • {{ $candidate->city }}, {{ $candidate->country_code }}</p>
    <p>{{ $candidate->relationship_intent }}</p>
    <p>{{ $candidate->bio }}</p>

    <form method="POST" action="#" class="stack">
        <button type="submit">Accept</button>
        <button type="submit">Reject</button>
    </form>
</article>
