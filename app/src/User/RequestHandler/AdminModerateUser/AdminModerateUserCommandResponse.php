<?php

declare(strict_types=1);

namespace App\User\RequestHandler\AdminModerateUser;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class AdminModerateUserCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $userId)
    {
    }
}
