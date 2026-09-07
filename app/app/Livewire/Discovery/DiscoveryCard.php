<?php

declare(strict_types=1);

namespace App\Livewire\Discovery;

use App\Discovery\RequestHandler\FindNextCandidate\FindNextCandidateQuery;
use App\Discovery\RequestHandler\FindNextCandidate\FindNextCandidateQueryResponse;
use App\Discovery\RequestHandler\RecordDiscoveryDecision\RecordDiscoveryDecisionCommand;
use App\Models\DatingProfile;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class DiscoveryCard extends Component
{
    /**
     * @var array{
     *     id: int,
     *     user_id: int,
     *     display_name: string,
     *     age: int,
     *     city: string,
     *     country_code: string,
     *     relationship_intent: string,
     *     bio: string,
     *     primary_photo_url: string|null,
     *     shared_tags: list<string>,
     *     public_tags: list<string>
     * }|null
     */
    public ?array $candidateData = null;

    public function mount(?DatingProfile $candidate): void
    {
        $this->candidateData = $this->serializeCandidate($candidate);
    }

    public function accept(CommandBusInterface $commandBus, QueryBusInterface $queryBus): void
    {
        $this->recordDecision('accepted', $commandBus, $queryBus);
    }

    public function reject(CommandBusInterface $commandBus, QueryBusInterface $queryBus): void
    {
        $this->recordDecision('rejected', $commandBus, $queryBus);
    }

    public function render(): View
    {
        return view('livewire.discovery.discovery-card');
    }

    private function recordDecision(string $decision, CommandBusInterface $commandBus, QueryBusInterface $queryBus): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (null === $user || null === $this->candidateData) {
            return;
        }

        $context = new SystemContext((string) $user->getKey(), request()->ip());

        $commandBus->dispatchPayload(RecordDiscoveryDecisionCommand::class, [
            'user_id' => $user->getKey(),
            'target_profile_id' => $this->candidateData['id'],
            'decision' => $decision,
        ], $context);

        $response = $queryBus->askPayload(FindNextCandidateQuery::class, ['user_id' => $user->getKey()], $context);

        if (!$response instanceof FindNextCandidateQueryResponse) {
            throw new \RuntimeException('Unexpected discovery response.');
        }

        $this->candidateData = $this->serializeCandidate($response->candidate);
    }

    /**
     * @return array{
     *     id: int,
     *     user_id: int,
     *     display_name: string,
     *     age: int,
     *     city: string,
     *     country_code: string,
     *     relationship_intent: string,
     *     bio: string,
     *     primary_photo_url: string|null,
     *     shared_tags: list<string>,
     *     public_tags: list<string>
     * }|null
     */
    private function serializeCandidate(?DatingProfile $candidate): ?array
    {
        if (null === $candidate) {
            return null;
        }

        $primaryPhoto = $candidate->photos()->where('is_primary', true)->orderBy('sort_order')->first()
            ?? $candidate->photos()->orderBy('sort_order')->first();
        $viewerProfile = Auth::user()?->datingProfile;
        $viewerPublicTagIds = $viewerProfile
            ? $viewerProfile->tags()->wherePivot('visibility', 'public')->pluck('profile_tags.id')->all()
            : [];
        $sharedTags = $candidate->tags()
            ->wherePivot('visibility', 'public')
            ->whereIn('profile_tags.id', $viewerPublicTagIds)
            ->orderBy('profile_tags.label')
            ->pluck('profile_tags.label')
            ->all();
        $publicTags = $candidate->tags()
            ->wherePivot('visibility', 'public')
            ->orderBy('profile_tags.label')
            ->pluck('profile_tags.label')
            ->all();

        return [
            'id' => (int) $candidate->getKey(),
            'user_id' => (int) $candidate->user_id,
            'display_name' => $candidate->display_name,
            'age' => $candidate->birthdate->age,
            'city' => $candidate->city,
            'country_code' => $candidate->country_code,
            'relationship_intent' => $candidate->relationship_intent,
            'bio' => (string) $candidate->bio,
            'primary_photo_url' => $primaryPhoto?->publicUrl(),
            'shared_tags' => array_values(array_map(static fn (mixed $tag): string => (string) $tag, $sharedTags)),
            'public_tags' => array_values(array_map(static fn (mixed $tag): string => (string) $tag, $publicTags)),
        ];
    }
}
