<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Connection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class MatchCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Connection $connection,
        public readonly int $conversationId,
    ) {
    }

    /**
     * @return list<Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(sprintf('users.%d', $this->connection->profileOne->user_id)),
            new PrivateChannel(sprintf('users.%d', $this->connection->profileTwo->user_id)),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'connection_id' => (int) $this->connection->getKey(),
            'conversation_id' => $this->conversationId,
            'matched_at' => $this->connection->matched_at?->toDateTimeString(),
            'profiles' => [
                [
                    'id' => (int) $this->connection->profileOne->getKey(),
                    'display_name' => $this->connection->profileOne->display_name,
                    'city' => $this->connection->profileOne->city,
                ],
                [
                    'id' => (int) $this->connection->profileTwo->getKey(),
                    'display_name' => $this->connection->profileTwo->display_name,
                    'city' => $this->connection->profileTwo->city,
                ],
            ],
        ];
    }
}
