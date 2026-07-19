<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Dto\ValueObject\Context\ContextInterface;

interface QueryBusInterface
{
    public function ask(QueryInterface $query, ContextInterface $context): QueryResponseInterface;

    /**
     * @param array<string, mixed> $payload
     */
    public function askPayload(string $class, array $payload, ContextInterface $context): QueryResponseInterface;
}
