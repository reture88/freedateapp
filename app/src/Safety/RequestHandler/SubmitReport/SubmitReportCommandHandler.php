<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\SubmitReport;

use App\Safety\DomainService\SubmitReportService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class SubmitReportCommandHandler implements CommandHandlerInterface
{
    public function __construct(private SubmitReportService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): SubmitReportCommandResponse
    {
        if (!$command instanceof SubmitReportCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', SubmitReportCommand::class));
        }

        $report = $this->service->handle($command);

        return new SubmitReportCommandResponse((int) $report->getKey());
    }
}
