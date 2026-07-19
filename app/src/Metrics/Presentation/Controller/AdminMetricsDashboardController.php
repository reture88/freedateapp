<?php

declare(strict_types=1);

namespace App\Metrics\Presentation\Controller;

use App\Metrics\DomainService\MetricsDashboardService;
use Illuminate\Contracts\View\View;

final readonly class AdminMetricsDashboardController
{
    public function __construct(private MetricsDashboardService $service)
    {
    }

    public function __invoke(): View
    {
        return view('admin.metrics.index', [
            'metrics' => $this->service->handle(),
        ]);
    }
}
