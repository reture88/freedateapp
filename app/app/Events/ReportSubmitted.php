<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ReportSubmitted implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly Report $report)
    {
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('admin.reports');
    }

    /**
     * @return array<string, int|string|null>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => (int) $this->report->getKey(),
            'reporter_user_id' => (int) $this->report->reporter_user_id,
            'target_type' => (string) $this->report->target_type,
            'target_id' => (int) $this->report->target_id,
            'category' => (string) $this->report->category,
            'status' => (string) $this->report->status,
            'created_at' => $this->report->created_at?->toDateTimeString(),
        ];
    }
}
