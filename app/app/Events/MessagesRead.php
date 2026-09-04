<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class MessagesRead implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @param list<int> $messageIds
     */
    public function __construct(
        public readonly int $conversationId,
        public readonly int $readerUserId,
        public readonly array $messageIds,
        public readonly string $readAt,
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel(sprintf('conversations.%d', $this->conversationId));
    }

    /**
     * @return array{conversation_id: int, reader_user_id: int, message_ids: list<int>, read_at: string}
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'reader_user_id' => $this->readerUserId,
            'message_ids' => $this->messageIds,
            'read_at' => $this->readAt,
        ];
    }
}
