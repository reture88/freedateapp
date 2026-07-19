@php($primaryPhoto = $candidate->photos()->where('is_primary', true)->orderBy('sort_order')->first() ?? $candidate->photos()->orderBy('sort_order')->first())
@php($viewerProfile = auth()->user()?->datingProfile)
@php($viewerPublicTagIds = $viewerProfile ? $viewerProfile->tags()->wherePivot('visibility', 'public')->pluck('profile_tags.id')->all() : [])
@php($sharedPublicTags = $candidate->tags()->wherePivot('visibility', 'public')->whereIn('profile_tags.id', $viewerPublicTagIds)->orderBy('profile_tags.label')->get())

<article class="panel">
    @if ($primaryPhoto)
        <img src="{{ $primaryPhoto->publicUrl() }}" alt="{{ $candidate->display_name }} profile photo">
    @endif
    <h1>{{ $candidate->display_name }}</h1>
    <p>{{ $candidate->birthdate->age }} • {{ $candidate->city }}, {{ $candidate->country_code }}</p>
    <p>{{ $candidate->relationship_intent }}</p>
    <p>{{ $candidate->bio }}</p>
    @if ($sharedPublicTags->isNotEmpty())
        <section class="shared-interest-list">
            <h2>Shared interests</h2>
            <div class="tag-list">
                @foreach ($sharedPublicTags as $tag)
                    <span>{{ $tag->label }}</span>
                @endforeach
            </div>
        </section>
    @endif

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
        @include('components.confirm-button', [
            'label' => 'Report profile',
            'message' => 'Submit a safety report for this profile?',
            'class' => 'button button--secondary',
        ])
    </form>
    <form method="POST" action="{{ route('blocks.submit') }}" class="stack">
        @csrf
        <input type="hidden" name="blocked_user_id" value="{{ $candidate->user_id }}">
        <input type="hidden" name="reason_category" value="safety">
        @include('components.confirm-button', [
            'label' => 'Block user',
            'message' => 'Block this user and hide any shared chat?',
        ])
    </form>
</article>
