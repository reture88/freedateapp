<?php

declare(strict_types=1);

namespace App\Safety\Presentation\Controller\Admin;

use App\Models\Report;
use Illuminate\Contracts\View\View;

final class AdminReportShowController
{
    public function __invoke(Report $report): View
    {
        return view('admin.reports.show', [
            'report' => $report->load('reporter', 'reviewer'),
        ]);
    }
}
