<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\UpdateProfileVisibility;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class UpdateProfileVisibilityCommandResponse implements CommandResponseInterface
{
    public function __construct(
        public int $profileId,
        public string $visibilityStatus,
    ) {
    }
}
