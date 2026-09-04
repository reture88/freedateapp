<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\AdminModerateReport;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class AdminModerateReportCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $reportId)
    {
    }
}
