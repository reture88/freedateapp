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
        $conversation = Conversation::query()
            ->with([
                'connection.profileOne.photos',
                'connection.profileTwo.photos',
                'messages' => static fn ($query) => $query->orderBy('created_at')->orderBy('id'),
            ])
            ->findOrFail($query->conversationId);
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

        if (!$viewerProfile->user->is_active || null !== $viewerProfile->user->deletion_requested_at) {
            throw new \InvalidArgumentException('Conversation is not available.');
        }

        if (!$otherProfile->user->is_active || null !== $otherProfile->user->deletion_requested_at) {
            throw new \InvalidArgumentException('Conversation is not available.');
        }

        return new ConversationView($conversation, $otherProfile);
    }
}
