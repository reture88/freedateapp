<?php

declare(strict_types=1);

namespace App\User\RequestHandler\RequestAccountDeletion;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class RequestAccountDeletionCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $userId)
    {
    }
}
