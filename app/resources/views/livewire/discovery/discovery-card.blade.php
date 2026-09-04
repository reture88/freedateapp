<div>
    @if ($candidateData)
        <article class="panel">
            @include('components.profile-card', [
                'displayName' => $candidateData['display_name'],
                'age' => $candidateData['age'],
                'city' => $candidateData['city'],
                'countryCode' => $candidateData['country_code'],
                'relationshipIntent' => $candidateData['relationship_intent'],
                'bio' => $candidateData['bio'],
                'primaryPhotoUrl' => $candidateData['primary_photo_url'],
                'sharedTags' => $candidateData['shared_tags'],
            ])

            <div class="stack">
                <button class="button button--primary" type="button" wire:click="accept" wire:loading.attr="disabled" wire:target="accept,reject">Accept</button>
                <button class="button button--secondary" type="button" wire:click="reject" wire:loading.attr="disabled" wire:target="accept,reject">Reject</button>
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
