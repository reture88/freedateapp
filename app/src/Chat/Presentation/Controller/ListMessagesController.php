<?php

declare(strict_types=1);

namespace App\Chat\Presentation\Controller;

use App\Connection\RequestHandler\ListMatches\ListMatchesQuery;
use App\Connection\RequestHandler\ListMatches\ListMatchesQueryResponse;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final readonly class ListMessagesController
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $response = $this->queryBus->askPayload(ListMatchesQuery::class, ['user_id' => $user->getKey()], new SystemContext((string) $user->getKey(), $request->ip()));

        if (!$response instanceof ListMatchesQueryResponse) {
            throw new \RuntimeException('Unexpected message list response.');
        }

        return view('chat.index', ['conversations' => $response->matches]);
    }
}
