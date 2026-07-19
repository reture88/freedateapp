<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Dto\ValueObject\Context\ContextInterface;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command, ContextInterface $context): ?CommandResponseInterface;
}
