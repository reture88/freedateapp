<?php

declare(strict_types=1);

namespace App\Safety\Presentation\Controller\Admin;

use App\Models\Report;
use App\Models\User;
use App\Safety\DomainService\AdminModerateReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class AdminReportActionController
{
    public function __construct(private AdminModerateReportService $service)
    {
    }

    public function __invoke(Request $request, Report $report): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->service->handle($user, $report, (string) $request->input('action'), $request->input('resolution') ? (string) $request->input('resolution') : null);

        return redirect(route('admin.reports.show', $report));
    }
}
