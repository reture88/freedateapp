<?php

declare(strict_types=1);

namespace App\User\RequestHandler\UpdateProfile;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class UpdateProfileCommandResponse implements CommandResponseInterface
{
    public function __construct(
        public string $userId,
        public string $email,
    ) {
    }
}
