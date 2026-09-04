<?php

declare(strict_types=1);

namespace App\Livewire\Connection;

use App\Connection\Dto\MatchListItem;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

final class MatchList extends Component
{
    public int $userId = 0;

    public int $profileId = 0;

    /**
     * @var list<array{connection_id: int, conversation_id: int, display_name: string, city: string, matched_at: string|null}>
     */
    public array $matches = [];

    /**
     * @param list<MatchListItem> $matches
     */
    public function mount(array $matches): void
    {
        $user = Auth::user();
        $this->userId = $user instanceof User ? (int) $user->getKey() : 0;
        $this->profileId = $user instanceof User ? (int) ($user->datingProfile?->getKey() ?? 0) : 0;
        $this->matches = array_map(static fn (MatchListItem $match): array => [
            'connection_id' => $match->connectionId,
            'conversation_id' => $match->conversationId,
            'display_name' => $match->displayName,
            'city' => $match->city,
            'matched_at' => $match->matchedAt,
        ], $matches);
    }

    public function render(): View
    {
        return view('livewire.connection.match-list');
    }

    /**
     * @param array{
     *     connection_id?: int,
     *     conversation_id?: int,
     *     matched_at?: string|null,
     *     profiles?: list<array{id?: int, display_name?: string, city?: string}>
     * } $event
     */
    #[On('echo-private:users.{userId},MatchCreated')]
    public function prependBroadcastedMatch(array $event): void
    {
        $connectionId = (int) ($event['connection_id'] ?? 0);

        if ($connectionId < 1) {
            return;
        }

        foreach ($this->matches as $match) {
            if ($match['connection_id'] === $connectionId) {
                return;
            }
        }

        $otherProfile = $this->otherProfileFromEvent($event['profiles'] ?? []);

        if (null === $otherProfile) {
            return;
        }

        array_unshift($this->matches, [
            'connection_id' => $connectionId,
            'conversation_id' => (int) ($event['conversation_id'] ?? 0),
            'display_name' => (string) ($otherProfile['display_name'] ?? ''),
            'city' => (string) ($otherProfile['city'] ?? ''),
            'matched_at' => isset($event['matched_at']) ? (string) $event['matched_at'] : null,
        ]);
    }

    /**
     * @param list<array{id?: int, display_name?: string, city?: string}> $profiles
     *
     * @return array{id?: int, display_name?: string, city?: string}|null
     */
    private function otherProfileFromEvent(array $profiles): ?array
    {
        foreach ($profiles as $profile) {
            if ((int) ($profile['id'] ?? 0) !== $this->profileId) {
                return $profile;
            }
        }

        return null;
    }
}
