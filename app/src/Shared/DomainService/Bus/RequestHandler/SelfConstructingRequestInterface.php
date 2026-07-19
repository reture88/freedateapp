<?php

declare(strict_types=1);

namespace App\Shared\DomainService\Bus\RequestHandler;

interface SelfConstructingRequestInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static;
}
