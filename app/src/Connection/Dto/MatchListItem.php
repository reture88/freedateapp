<?php

declare(strict_types=1);

namespace App\Connection\Dto;

final readonly class MatchListItem
{
    public function __construct(
        public int $connectionId,
        public int $conversationId,
        public string $displayName,
        public string $city,
        public ?string $matchedAt,
    ) {
    }
}
