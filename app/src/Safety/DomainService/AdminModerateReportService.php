<?php

declare(strict_types=1);

namespace App\Safety\DomainService;

use App\Models\AuditLog;
use App\Models\DatingProfile;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Carbon;

final class AdminModerateReportService
{
    public function handle(User $actor, Report $report, string $action, ?string $resolution): Report
    {
        $before = $report->toArray();

        if ('hide_profile' === $action && 'profile' === $report->target_type) {
            DatingProfile::query()
                ->whereKey($report->target_id)
                ->update([
                    'visibility_status' => 'hidden',
                    'moderation_status' => 'rejected',
                ]);
        }

        $report->forceFill([
            'status' => 'resolved',
            'reviewer_user_id' => $actor->getKey(),
            'resolution' => $resolution ?? $action,
            'resolved_at' => Carbon::now(),
        ])->save();

        AuditLog::query()->create([
            'actor_user_id' => $actor->getKey(),
            'action' => $action,
            'target_type' => 'report',
            'target_id' => $report->getKey(),
            'before' => $before,
            'after' => $report->fresh()?->toArray(),
        ]);

        return $report;
    }
}
