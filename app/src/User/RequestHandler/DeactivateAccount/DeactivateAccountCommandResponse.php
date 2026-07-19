<?php

declare(strict_types=1);

namespace App\User\RequestHandler\DeactivateAccount;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class DeactivateAccountCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $userId)
    {
    }
}
