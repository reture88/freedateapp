<?php

declare(strict_types=1);

namespace App\Chat\RequestHandler\ShowConversation;

use App\Chat\Dto\ConversationView;
use App\Shared\Infrastructure\Bus\Query\QueryResponseInterface;

final readonly class ShowConversationQueryResponse implements QueryResponseInterface
{
    public function __construct(public ConversationView $conversationView)
    {
    }
}
