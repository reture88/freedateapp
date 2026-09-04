<div>
    @if ($candidateData)
        <article class="panel">
            @if ($candidateData['primary_photo_url'])
                <img src="{{ $candidateData['primary_photo_url'] }}" alt="{{ $candidateData['display_name'] }} profile photo">
            @endif

            <h1>{{ $candidateData['display_name'] }}</h1>
            <p>{{ $candidateData['age'] }} • {{ $candidateData['city'] }}, {{ $candidateData['country_code'] }}</p>
            <p>{{ $candidateData['relationship_intent'] }}</p>
            <p>{{ $candidateData['bio'] }}</p>

            @if ($candidateData['shared_tags'] !== [])
                <section class="shared-interest-list">
                    <h2>Shared interests</h2>
                    <div class="tag-list">
                        @foreach ($candidateData['shared_tags'] as $tag)
                            <span>{{ $tag }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="stack">
                <button type="button" wire:click="accept" wire:loading.attr="disabled" wire:target="accept,reject">Accept</button>
                <button type="button" wire:click="reject" wire:loading.attr="disabled" wire:target="accept,reject">Reject</button>
            </div>

            <form method="POST" action="{{ route('reports.submit') }}" class="stack">
                @csrf
                <input type="hidden" name="target_type" value="profile">
                <input type="hidden" name="target_id" value="{{ $candidateData['id'] }}">
                <input type="hidden" name="category" value="profile_concern">
                @include('components.confirm-button', [
                    'label' => 'Report profile',
                    'message' => 'Submit a safety report for this profile?',
                    'class' => 'button button--secondary',
                ])
            </form>

            <form method="POST" action="{{ route('blocks.submit') }}" class="stack">
                @csrf
                <input type="hidden" name="blocked_user_id" value="{{ $candidateData['user_id'] }}">
                <input type="hidden" name="reason_category" value="safety">
                @include('components.confirm-button', [
                    'label' => 'Block user',
                    'message' => 'Block this user and hide any shared chat?',
                ])
            </form>
        </article>
    @else
        @include('components.empty-state', [
            'title' => 'No profiles available right now.',
            'message' => 'Update your preferences or check back later.',
            'actionUrl' => route('dating-preferences.edit'),
            'actionLabel' => 'Edit preferences',
        ])
    @endif
</div>
