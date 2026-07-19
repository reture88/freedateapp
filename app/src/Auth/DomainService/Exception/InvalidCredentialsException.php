<?php

declare(strict_types=1);

namespace App\Auth\DomainService\Exception;

use App\Shared\Infrastructure\Translator\Translator;
use RuntimeException;

final class InvalidCredentialsException extends RuntimeException
{
    public static function create(): self
    {
        return new self(Translator::transStatic('domain.auth.invalid_credentials'));
    }
}
