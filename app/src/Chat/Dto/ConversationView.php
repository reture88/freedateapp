<?php

declare(strict_types=1);

namespace App\Chat\Dto;

use App\Models\Conversation;
use App\Models\DatingProfile;

final readonly class ConversationView
{
    public function __construct(
        public Conversation $conversation,
        public DatingProfile $otherProfile,
    ) {
    }
}
