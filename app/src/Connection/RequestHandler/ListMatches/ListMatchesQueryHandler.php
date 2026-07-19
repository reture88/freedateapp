<?php

declare(strict_types=1);

namespace App\Connection\RequestHandler\ListMatches;

use App\Connection\DomainService\ListMatchesService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\QueryInterface;

final readonly class ListMatchesQueryHandler implements QueryHandlerInterface
{
    public function __construct(private ListMatchesService $service)
    {
    }

    public function handle(QueryInterface $query, ContextInterface $context): ListMatchesQueryResponse
    {
        if (!$query instanceof ListMatchesQuery) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', ListMatchesQuery::class));
        }

        return new ListMatchesQueryResponse($this->service->handle($query));
    }
}
