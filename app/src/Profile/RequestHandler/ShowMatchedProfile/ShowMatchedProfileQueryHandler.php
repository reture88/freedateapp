<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\ShowMatchedProfile;

use App\Profile\DomainService\ShowMatchedProfileService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\QueryInterface;

final readonly class ShowMatchedProfileQueryHandler implements QueryHandlerInterface
{
    public function __construct(private ShowMatchedProfileService $service)
    {
    }

    public function handle(QueryInterface $query, ContextInterface $context): ShowMatchedProfileQueryResponse
    {
        if (!$query instanceof ShowMatchedProfileQuery) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', ShowMatchedProfileQuery::class));
        }

        return new ShowMatchedProfileQueryResponse($this->service->handle($query));
    }
}
