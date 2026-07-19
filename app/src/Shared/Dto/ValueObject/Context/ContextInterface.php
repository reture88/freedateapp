<?php

declare(strict_types=1);

namespace App\Shared\Dto\ValueObject\Context;

interface ContextInterface
{
    public function userId(): ?string;

    public function ipAddress(): ?string;
}
