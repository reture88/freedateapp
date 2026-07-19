<?php

declare(strict_types=1);

namespace App\Chat\RequestHandler\SendMessage;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class SendMessageCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $messageId)
    {
    }
}
