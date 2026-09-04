<?php

declare(strict_types=1);

namespace App\Chat\RequestHandler\MarkConversationRead;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class MarkConversationReadCommandResponse implements CommandResponseInterface
{
    /**
     * @param list<int> $messageIds
     */
    public function __construct(public array $messageIds)
    {
    }
}
