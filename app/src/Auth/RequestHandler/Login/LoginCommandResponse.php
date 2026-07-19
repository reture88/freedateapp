<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Login;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class LoginCommandResponse implements CommandResponseInterface
{
    public function __construct(
        public string $userId,
        public string $email,
    ) {
    }
}
