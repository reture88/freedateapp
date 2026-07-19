<?php

declare(strict_types=1);

namespace App\Discovery\RequestHandler\FindNextCandidate;

use App\Discovery\DomainService\FindNextCandidateService;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\QueryInterface;

final readonly class FindNextCandidateQueryHandler implements QueryHandlerInterface
{
    public function __construct(private FindNextCandidateService $service)
    {
    }

    public function handle(QueryInterface $query, ContextInterface $context): FindNextCandidateQueryResponse
    {
        if (!$query instanceof FindNextCandidateQuery) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', FindNextCandidateQuery::class));
        }

        $profile = User::query()->findOrFail($query->userId)->datingProfile()->firstOrFail();

        return new FindNextCandidateQueryResponse($this->service->handle($profile));
    }
}
