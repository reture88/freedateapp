<?php

declare(strict_types=1);

namespace App\Safety\Presentation\Controller\Admin;

use App\Models\Report;
use Illuminate\Contracts\View\View;

final class AdminReportIndexController
{
    public function __invoke(): View
    {
        return view('admin.reports.index', [
            'reports' => Report::query()
                ->where('status', 'open')
                ->with('reporter')
                ->latest()
                ->paginate(25),
        ]);
    }
}
