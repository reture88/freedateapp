<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Dto\ValueObject\Context\ContextInterface;

interface QueryHandlerInterface
{
    public function handle(QueryInterface $query, ContextInterface $context): QueryResponseInterface;
}
