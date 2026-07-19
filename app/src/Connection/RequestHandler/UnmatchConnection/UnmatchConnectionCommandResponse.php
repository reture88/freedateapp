<?php

declare(strict_types=1);

namespace App\Connection\RequestHandler\UnmatchConnection;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class UnmatchConnectionCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $connectionId)
    {
    }
}
