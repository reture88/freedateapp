<?php

declare(strict_types=1);

namespace App\User\DomainService\Exception;

use App\Shared\Infrastructure\Translator\Translator;
use RuntimeException;

final class PasswordConfirmationMismatchException extends RuntimeException
{
    public static function create(): self
    {
        return new self(Translator::transStatic('domain.user.password_confirmation_mismatch'));
    }
}
