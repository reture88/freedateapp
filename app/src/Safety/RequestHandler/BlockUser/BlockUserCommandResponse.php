<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\BlockUser;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class BlockUserCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $blockId)
    {
    }
}
