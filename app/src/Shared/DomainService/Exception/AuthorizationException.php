<?php

declare(strict_types=1);

namespace App\Shared\DomainService\Exception;

use App\Shared\Infrastructure\Translator\Translator;

final class AuthorizationException extends DomainException
{
    public static function denied(): self
    {
        return new self(Translator::transStatic('domain.shared.authorization_denied'));
    }
}
