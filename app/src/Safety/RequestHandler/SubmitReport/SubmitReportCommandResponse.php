<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\SubmitReport;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class SubmitReportCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $reportId)
    {
    }
}
