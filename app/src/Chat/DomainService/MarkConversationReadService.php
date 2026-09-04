<?php

declare(strict_types=1);

namespace App\Chat\DomainService;

use App\Chat\RequestHandler\MarkConversationRead\MarkConversationReadCommand;
use App\Events\MessagesRead;
use App\Models\Message;
use Illuminate\Support\Carbon;

final class MarkConversationReadService
{
    public function __construct(private ShowConversationService $conversationService)
    {
    }

    /**
     * @return list<int>
     */
    public function handle(MarkConversationReadCommand $command): array
    {
        $conversation = $this->conversationService->handle($command->toConversationQuery())->conversation;
        $readAt = Carbon::now();
        $messageIds = $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_user_id', '<>', $command->userId)
            ->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->values()
            ->all();

        if ([] === $messageIds) {
            return [];
        }

        Message::query()
            ->whereIn('id', $messageIds)
            ->update(['read_at' => $readAt]);

        MessagesRead::dispatch((int) $conversation->getKey(), $command->userId, $messageIds, $readAt->toDateTimeString());

        return $messageIds;
    }
}
