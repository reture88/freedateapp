<?php

declare(strict_types=1);

namespace App\Chat\RequestHandler\ShowConversation;

use App\Chat\DomainService\ShowConversationService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\QueryInterface;

final readonly class ShowConversationQueryHandler implements QueryHandlerInterface
{
    public function __construct(private ShowConversationService $service)
    {
    }

    public function handle(QueryInterface $query, ContextInterface $context): ShowConversationQueryResponse
    {
        if (!$query instanceof ShowConversationQuery) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', ShowConversationQuery::class));
        }

        return new ShowConversationQueryResponse($this->service->handle($query));
    }
}
