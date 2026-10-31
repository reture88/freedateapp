<?php

declare(strict_types=1);

namespace App\Connection\DomainService;

use App\Models\Connection;
use App\Models\User;
use App\Connection\Dto\MatchListItem;
use App\Connection\RequestHandler\ListMatches\ListMatchesQuery;

final class ListMatchesService
{
    /**
     * @return list<MatchListItem>
     */
    public function handle(ListMatchesQuery $query): array
    {
        $viewerProfile = User::query()->findOrFail($query->userId)->datingProfile()->firstOrFail();

        /** @var iterable<Connection> $connections */
        $connections = Connection::query()
            ->where('status', 'matched')
            ->where(static function ($builder) use ($viewerProfile): void {
                $builder
                    ->where('profile_one_id', $viewerProfile->getKey())
                    ->orWhere('profile_two_id', $viewerProfile->getKey());
            })
            ->with([
                'profileOne.photos',
                'profileOne.user',
                'profileTwo.photos',
                'profileTwo.user',
                'conversation.messages' => static fn ($query) => $query->orderBy('created_at')->orderBy('id'),
            ])
            ->orderByDesc('matched_at')
            ->get();

        $matches = [];

        foreach ($connections as $connection) {
            $otherProfile = (int) $connection->profile_one_id === (int) $viewerProfile->getKey()
                ? $connection->profileTwo
                : $connection->profileOne;

            if ('visible' !== $otherProfile->visibility_status || 'approved' !== $otherProfile->moderation_status || !$otherProfile->hasAdultBirthdate()) {
                continue;
            }

            if (!$otherProfile->user->hasActiveAccount() || !$otherProfile->user->hasVerifiedEmail()) {
                continue;
            }

            $conversation = $connection->conversation ?? $connection->conversation()->firstOrCreate([]);
            $primaryPhoto = $otherProfile->photos->where('is_primary', true)->sortBy('sort_order')->first()
                ?? $otherProfile->photos->sortBy('sort_order')->first();
            $unreadCount = $conversation->messages
                ->filter(static fn ($message): bool => null === $message->read_at && (int) ($message->sender_user_id ?? 0) !== $query->userId)
                ->count();
            $latestMessage = $conversation->messages->last();

            $matches[] = new MatchListItem(
                (int) $connection->getKey(),
                (int) $conversation->getKey(),
                $otherProfile->display_name,
                $otherProfile->birthdate->age,
                $otherProfile->city,
                $primaryPhoto?->publicUrl(),
                $connection->matched_at?->toDateTimeString(),
                $unreadCount,
                null === $latestMessage ? null : (string) $latestMessage->body,
                null === $latestMessage ? null : $latestMessage->created_at?->toDateTimeString(),
            );
        }

        return $matches;
    }
}
