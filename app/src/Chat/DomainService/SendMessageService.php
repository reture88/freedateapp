<?php

declare(strict_types=1);

namespace App\Chat\DomainService;

use App\Chat\RequestHandler\SendMessage\SendMessageCommand;
use App\Models\Message;

final class SendMessageService
{
    public function __construct(private ShowConversationService $conversationService)
    {
    }

    public function handle(SendMessageCommand $command): Message
    {
        $conversation = $this->conversationService->handle($command->toConversationQuery())->conversation;

        return $conversation->messages()->create([
            'sender_user_id' => $command->userId,
            'sender_type' => 'user',
            'body' => $command->body,
        ]);
    }
}
