<?php

declare(strict_types=1);

namespace App\Safety\DomainService;

use App\Events\ReportSubmitted;
use App\Models\Report;
use App\Safety\RequestHandler\SubmitReport\SubmitReportCommand;

final class SubmitReportService
{
    public function handle(SubmitReportCommand $command): Report
    {
        /** @var Report $report */
        $report = Report::query()->create([
            'reporter_user_id' => $command->reporterUserId,
            'target_type' => $command->targetType,
            'target_id' => $command->targetId,
            'category' => $command->category,
            'note' => $command->note,
            'status' => 'open',
        ]);

        ReportSubmitted::dispatch($report);

        return $report;
    }
}
