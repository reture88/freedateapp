<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Dto\ValueObject\Context\ContextInterface;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command, ContextInterface $context): ?CommandResponseInterface;

    /**
     * @param array<string, mixed> $payload
     */
    public function dispatchPayload(string $class, array $payload, ContextInterface $context): ?CommandResponseInterface;
}
