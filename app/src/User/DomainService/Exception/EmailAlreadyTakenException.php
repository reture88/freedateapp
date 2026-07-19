<?php

declare(strict_types=1);

namespace App\User\DomainService\Exception;

use App\Shared\Infrastructure\Translator\Translator;
use App\User\Dto\ValueObject\EmailAddress;
use RuntimeException;

final class EmailAlreadyTakenException extends RuntimeException
{
    public static function create(EmailAddress $email): self
    {
        return new self(Translator::transStatic('domain.user.email_already_taken', replace: ['email' => $email->value()]));
    }
}
