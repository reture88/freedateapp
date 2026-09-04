<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly Message $message)
    {
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel(sprintf('conversations.%d', $this->message->conversation_id));
    }

    /**
     * @return array<string, int|string|null>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => (int) $this->message->getKey(),
            'conversation_id' => (int) $this->message->conversation_id,
            'sender_user_id' => null === $this->message->sender_user_id ? null : (int) $this->message->sender_user_id,
            'sender_type' => (string) $this->message->sender_type,
            'body' => (string) $this->message->body,
            'read_at' => $this->message->read_at?->toDateTimeString(),
        ];
    }
}
