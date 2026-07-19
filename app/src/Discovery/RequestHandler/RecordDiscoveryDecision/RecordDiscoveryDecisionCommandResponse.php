<?php

declare(strict_types=1);

namespace App\Discovery\RequestHandler\RecordDiscoveryDecision;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class RecordDiscoveryDecisionCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $decisionId)
    {
    }
}
