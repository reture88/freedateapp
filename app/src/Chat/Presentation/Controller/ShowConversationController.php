<?php

declare(strict_types=1);

namespace App\Chat\Presentation\Controller;

use App\Chat\RequestHandler\ShowConversation\ShowConversationQuery;
use App\Chat\RequestHandler\ShowConversation\ShowConversationQueryResponse;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final readonly class ShowConversationController
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    public function __invoke(Request $request, int $conversation): View
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $response = $this->queryBus->askPayload(ShowConversationQuery::class, [
                'user_id' => $user->getKey(),
                'conversation_id' => $conversation,
            ], new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (\InvalidArgumentException) {
            abort(403);
        }

        if (!$response instanceof ShowConversationQueryResponse) {
            throw new \RuntimeException('Unexpected conversation response.');
        }

        return view('chat.show', [
            'conversationView' => $response->conversationView,
        ]);
    }
}
