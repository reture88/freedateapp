<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Register;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class RegisterCommandResponse implements CommandResponseInterface
{
    public function __construct(
        public string $userId,
        public string $email,
    ) {
    }
}
