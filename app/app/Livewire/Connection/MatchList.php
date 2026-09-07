<?php

declare(strict_types=1);

namespace App\Livewire\Connection;

use App\Connection\Dto\MatchListItem;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

final class MatchList extends Component
{
    public int $userId = 0;

    public int $profileId = 0;

    /**
     * @var list<array{connection_id: int, conversation_id: int, display_name: string, age: int|null, city: string, primary_photo_url: string|null, matched_at: string|null, unread_count: int, latest_message_preview: string|null, latest_message_at: string|null, state: string, status_label: string}>
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
        $this->matches = array_map(fn (MatchListItem $match): array => [
            'connection_id' => $match->connectionId,
            'conversation_id' => $match->conversationId,
            'display_name' => $match->displayName,
            'age' => $match->age,
            'city' => $match->city,
            'primary_photo_url' => $match->primaryPhotoUrl,
            'matched_at' => $match->matchedAt,
            'unread_count' => $match->unreadCount,
            'latest_message_preview' => $match->latestMessagePreview,
            'latest_message_at' => $match->latestMessageAt,
            'state' => $this->matchState($match->matchedAt, $match->unreadCount),
            'status_label' => $this->matchStatusLabel($match->city, $match->matchedAt, $match->unreadCount),
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
            'age' => null,
            'city' => (string) ($otherProfile['city'] ?? ''),
            'primary_photo_url' => null,
            'matched_at' => isset($event['matched_at']) ? (string) $event['matched_at'] : null,
            'unread_count' => 0,
            'latest_message_preview' => null,
            'latest_message_at' => null,
            'state' => $this->matchState(isset($event['matched_at']) ? (string) $event['matched_at'] : null, 0),
            'status_label' => 'New match',
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

    private function matchState(?string $matchedAt, int $unreadCount): string
    {
        if ($unreadCount > 0) {
            return 'unread';
        }

        if (null !== $matchedAt && CarbonImmutable::parse($matchedAt)->greaterThanOrEqualTo(CarbonImmutable::now()->subDays(7))) {
            return 'new';
        }

        return 'default';
    }

    private function matchStatusLabel(string $city, ?string $matchedAt, int $unreadCount): string
    {
        if ($unreadCount > 0) {
            return 1 === $unreadCount ? 'Unread message' : sprintf('%d unread messages', $unreadCount);
        }

        if (null !== $matchedAt && CarbonImmutable::parse($matchedAt)->greaterThanOrEqualTo(CarbonImmutable::now()->subDays(7))) {
            return 'New match';
        }

        return '' !== $city ? $city : 'Recently active';
    }
}
