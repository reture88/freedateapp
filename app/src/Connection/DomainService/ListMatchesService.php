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
            ->with(['profileOne', 'profileTwo', 'conversation'])
            ->orderByDesc('matched_at')
            ->get();

        $matches = [];

        foreach ($connections as $connection) {
            $otherProfile = (int) $connection->profile_one_id === (int) $viewerProfile->getKey()
                ? $connection->profileTwo
                : $connection->profileOne;

            if ('visible' !== $otherProfile->visibility_status || 'approved' !== $otherProfile->moderation_status) {
                continue;
            }

            $matches[] = new MatchListItem(
                (int) $connection->getKey(),
                (int) $connection->conversation()->firstOrCreate([])->getKey(),
                $otherProfile->display_name,
                $otherProfile->city,
                $connection->matched_at?->toDateTimeString(),
            );
        }

        return $matches;
    }
}
