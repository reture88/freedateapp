<?php

declare(strict_types=1);

namespace App\Chat\DomainService;

use App\Chat\Dto\ConversationView;
use App\Chat\RequestHandler\ShowConversation\ShowConversationQuery;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\User;

final class ShowConversationService
{
    public function handle(ShowConversationQuery $query): ConversationView
    {
        $viewerProfile = User::query()->findOrFail($query->userId)->datingProfile()->firstOrFail();
        $conversation = Conversation::query()->with(['connection.profileOne', 'connection.profileTwo', 'messages'])->findOrFail($query->conversationId);
        $connection = $conversation->connection;

        if ('matched' !== $connection->status) {
            throw new \InvalidArgumentException('Conversation is not available.');
        }

        if ((int) $connection->profile_one_id !== (int) $viewerProfile->getKey() && (int) $connection->profile_two_id !== (int) $viewerProfile->getKey()) {
            throw new \InvalidArgumentException('Conversation is not available.');
        }

        $otherProfile = (int) $connection->profile_one_id === (int) $viewerProfile->getKey()
            ? $connection->profileTwo
            : $connection->profileOne;

        return new ConversationView($conversation, $otherProfile);
    }
}
