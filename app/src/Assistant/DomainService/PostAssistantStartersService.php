<?php

declare(strict_types=1);

namespace App\Assistant\DomainService;

use App\Assistant\Dto\AssistantStarterContext;
use App\Events\MessageSent;
use App\Models\Connection;
use App\Models\DatingProfile;
use App\Models\Message;
use Illuminate\Support\Carbon;

final class PostAssistantStartersService
{
    private const WAITING_PERIOD_HOURS = 6;

    public function __construct(private AssistantStarterService $starterService)
    {
    }

    public function handle(): int
    {
        $posted = 0;

        /** @var iterable<Connection> $connections */
        $connections = Connection::query()
            ->where('status', 'matched')
            ->whereNotNull('matched_at')
            ->where('matched_at', '<=', Carbon::now()->subHours(self::WAITING_PERIOD_HOURS))
            ->with(['profileOne', 'profileTwo', 'conversation.messages'])
            ->get();

        foreach ($connections as $connection) {
            $conversation = $connection->conversation()->firstOrCreate([]);

            if ($conversation->messages()->exists()) {
                continue;
            }

            [$speaker, $target] = $this->speakerAndTarget($connection);

            if (!$speaker instanceof DatingProfile || !$target instanceof DatingProfile) {
                continue;
            }

            $body = $this->starterService->handle(new AssistantStarterContext(
                speakerName: $speaker->display_name,
                targetName: $target->display_name,
                targetVisibleFacts: $this->visibleFacts($target),
                unsafeContext: false,
            ));

            if (null === $body) {
                continue;
            }

            /** @var Message $message */
            $message = Message::query()->create([
                'conversation_id' => $conversation->getKey(),
                'sender_user_id' => null,
                'sender_type' => 'assistant',
                'body' => $body,
            ]);
            MessageSent::dispatch($message);
            ++$posted;
        }

        return $posted;
    }

    /**
     * @return array{DatingProfile|null, DatingProfile|null}
     */
    private function speakerAndTarget(Connection $connection): array
    {
        if ($connection->profileOne->ai_wingman_enabled) {
            return [$connection->profileOne, $connection->profileTwo];
        }

        if ($connection->profileTwo->ai_wingman_enabled) {
            return [$connection->profileTwo, $connection->profileOne];
        }

        return [null, null];
    }

    /**
     * @return list<string>
     */
    private function visibleFacts(DatingProfile $profile): array
    {
        $bio = strtolower((string) $profile->bio);
        $facts = [];

        if (str_contains($bio, 'macska')) {
            $facts[] = 'three cats';
        }

        if (str_contains($bio, 'tura') || str_contains($bio, 'turak')) {
            $facts[] = 'weekend hikes';
        }

        return $facts;
    }
}
