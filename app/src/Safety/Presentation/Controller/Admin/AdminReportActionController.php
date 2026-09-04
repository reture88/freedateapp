<?php

declare(strict_types=1);

namespace App\Safety\Presentation\Controller\Admin;

use App\Models\Report;
use App\Models\User;
use App\Safety\RequestHandler\AdminModerateReport\AdminModerateReportCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class AdminReportActionController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request, Report $report): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->commandBus->dispatchPayload(AdminModerateReportCommand::class, [
            'actor_user_id' => $user->getKey(),
            'report_id' => $report->getKey(),
            'action' => $request->input('action'),
            'resolution' => $request->input('resolution'),
        ], new SystemContext((string) $user->getKey(), $request->ip()));

        return redirect(route('admin.reports.show', $report));
    }
}
