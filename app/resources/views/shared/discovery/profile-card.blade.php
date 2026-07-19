@php($primaryPhoto = $candidate->photos()->where('is_primary', true)->orderBy('sort_order')->first())

<article class="panel">
    @if ($primaryPhoto)
        <img src="{{ $primaryPhoto->path }}" alt="{{ $candidate->display_name }} profile photo">
    @endif
    <h1>{{ $candidate->display_name }}</h1>
    <p>{{ $candidate->birthdate->age }} • {{ $candidate->city }}, {{ $candidate->country_code }}</p>
    <p>{{ $candidate->relationship_intent }}</p>
    <p>{{ $candidate->bio }}</p>

    <form method="POST" action="{{ route('discovery.accept', ['targetProfile' => $candidate->getKey()]) }}" class="stack">
        @csrf
        <button type="submit">Accept</button>
    </form>
    <form method="POST" action="{{ route('discovery.reject', ['targetProfile' => $candidate->getKey()]) }}" class="stack">
        @csrf
        <button type="submit">Reject</button>
    </form>
    <form method="POST" action="{{ route('reports.submit') }}" class="stack">
        @csrf
        <input type="hidden" name="target_type" value="profile">
        <input type="hidden" name="target_id" value="{{ $candidate->getKey() }}">
        <input type="hidden" name="category" value="profile_concern">
        <button type="submit">Report profile</button>
    </form>
</article>
