<?php

declare(strict_types=1);

namespace App\Discovery\Presentation\Controller;

use App\Connection\RequestHandler\ListMatches\ListMatchesQuery;
use App\Connection\RequestHandler\ListMatches\ListMatchesQueryResponse;
use App\Discovery\RequestHandler\FindNextCandidate\FindNextCandidateQuery;
use App\Discovery\RequestHandler\FindNextCandidate\FindNextCandidateQueryResponse;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final readonly class ShowDiscoveryController
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $context = new SystemContext((string) $user->getKey(), $request->ip());
        $response = $this->queryBus->askPayload(FindNextCandidateQuery::class, ['user_id' => $user->getKey()], $context);

        if (!$response instanceof FindNextCandidateQueryResponse) {
            throw new \RuntimeException('Unexpected discovery response.');
        }

        $matchesResponse = $this->queryBus->askPayload(ListMatchesQuery::class, ['user_id' => $user->getKey()], $context);

        if (!$matchesResponse instanceof ListMatchesQueryResponse) {
            throw new \RuntimeException('Unexpected match list response.');
        }

        return view('discovery.show', [
            'candidate' => $response->candidate,
            'matches' => $matchesResponse->matches,
        ]);
    }
}
