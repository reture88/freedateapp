<?php

declare(strict_types=1);

namespace App\Metrics\DomainService;

use App\Models\MetricEvent;

final class MetricsDashboardService
{
    /**
     * @return array<string, int>
     */
    public function handle(): array
    {
        $counts = MetricEvent::query()
            ->selectRaw('event_name, count(*) as aggregate_count')
            ->groupBy('event_name')
            ->pluck('aggregate_count', 'event_name')
            ->map(static fn (mixed $value): int => (int) $value)
            ->all();

        return [
            'Registrations' => $counts['registration_completed'] ?? 0,
            'Completed profiles' => $counts['profile_completed'] ?? 0,
            'Active users' => $counts['active_user'] ?? 0,
            'Matches' => $counts['match_created'] ?? 0,
            'First messages' => $counts['first_message_sent'] ?? 0,
            'Reports' => $counts['report_submitted'] ?? 0,
            'Blocks' => $counts['block_created'] ?? 0,
            'Unmatches' => $counts['unmatch_created'] ?? 0,
            'Assistant starters' => $counts['assistant_starter_posted'] ?? 0,
        ];
    }
}
