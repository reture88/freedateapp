<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\AdminModerateReport;

use App\Models\Report;
use App\Models\User;
use App\Safety\DomainService\AdminModerateReportService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class AdminModerateReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(private AdminModerateReportService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): AdminModerateReportCommandResponse
    {
        if (!$command instanceof AdminModerateReportCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', AdminModerateReportCommand::class));
        }

        $actor = User::query()->findOrFail($command->actorUserId);
        $report = Report::query()->findOrFail($command->reportId);
        $moderatedReport = $this->service->handle($actor, $report, $command->action, $command->resolution);

        return new AdminModerateReportCommandResponse((int) $moderatedReport->getKey());
    }
}
