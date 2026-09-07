<?php

declare(strict_types=1);

namespace App\Connection\Dto;

final readonly class MatchListItem
{
    public function __construct(
        public int $connectionId,
        public int $conversationId,
        public string $displayName,
        public ?int $age,
        public string $city,
        public ?string $primaryPhotoUrl,
        public ?string $matchedAt,
        public int $unreadCount,
        public ?string $latestMessagePreview,
        public ?string $latestMessageAt,
    ) {
    }
}
