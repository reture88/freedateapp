<?php

declare(strict_types=1);

namespace App\Preference\RequestHandler\GetPreferenceSummary;

use App\Models\User;
use App\Preference\DomainService\BuildPreferenceSummaryService;
use App\Preference\DomainService\EnsureDefaultPreferenceService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\QueryInterface;

final readonly class GetPreferenceSummaryQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private BuildPreferenceSummaryService $summaryService,
        private EnsureDefaultPreferenceService $defaultPreferenceService,
    ) {
    }

    public function handle(QueryInterface $query, ContextInterface $context): GetPreferenceSummaryQueryResponse
    {
        if (!$query instanceof GetPreferenceSummaryQuery) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', GetPreferenceSummaryQuery::class));
        }

        $profile = User::query()->findOrFail($query->userId)->datingProfile()->firstOrFail();
        $preference = $this->defaultPreferenceService->handle($profile);
        $summary = $this->summaryService->handle($profile);

        return new GetPreferenceSummaryQueryResponse($preference, $summary);
    }
}
