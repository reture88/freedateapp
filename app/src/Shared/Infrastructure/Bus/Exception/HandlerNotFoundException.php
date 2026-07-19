<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Exception;

use RuntimeException;

final class HandlerNotFoundException extends RuntimeException
{
    public static function forRequest(object $request): self
    {
        return new self(sprintf('Handler not found for "%s".', $request::class));
    }
}
