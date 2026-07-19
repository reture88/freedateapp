<?php

declare(strict_types=1);

namespace App\Shared\Dto\ValueObject\Context;

final readonly class SystemContext implements ContextInterface
{
    public function __construct(
        private ?string $userId = null,
        private ?string $ipAddress = null,
    ) {
    }

    public function userId(): ?string
    {
        return $this->userId;
    }

    public function ipAddress(): ?string
    {
        return $this->ipAddress;
    }
}
