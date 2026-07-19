<?php

declare(strict_types=1);

namespace App\Metrics\DomainService;

use App\Models\MetricEvent;
use Illuminate\Support\Carbon;

final class MetricRecorderService
{
    /**
     * @param array<string, mixed> $payload
     */
    public function handle(string $eventName, ?int $userId = null, array $payload = []): MetricEvent
    {
        return MetricEvent::query()->create([
            'event_name' => $eventName,
            'user_id' => $userId,
            'payload' => $payload,
            'occurred_at' => Carbon::now(),
        ]);
    }
}
