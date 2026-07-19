<?php

declare(strict_types=1);

namespace App\Auth\DomainService\Exception;

use App\Shared\Infrastructure\Translator\Translator;
use RuntimeException;

final class PasswordConfirmationException extends RuntimeException
{
    public static function mismatch(): self
    {
        return new self(Translator::transStatic('domain.auth.password_confirmation_mismatch'));
    }
}
