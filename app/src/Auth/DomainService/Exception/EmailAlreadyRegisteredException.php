<?php

declare(strict_types=1);

namespace App\Auth\DomainService\Exception;

use App\Shared\Infrastructure\Translator\Translator;
use App\User\Dto\ValueObject\EmailAddress;
use RuntimeException;

final class EmailAlreadyRegisteredException extends RuntimeException
{
    public static function forEmail(EmailAddress $email): self
    {
        return new self(Translator::transStatic('domain.auth.email_already_registered', replace: ['email' => $email->value()]));
    }
}
