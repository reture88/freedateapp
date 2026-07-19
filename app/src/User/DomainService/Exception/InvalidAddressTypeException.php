<?php

declare(strict_types=1);

namespace App\User\DomainService\Exception;

use App\Shared\Infrastructure\Translator\Translator;
use RuntimeException;

final class InvalidAddressTypeException extends RuntimeException
{
    public static function create(string $type): self
    {
        return new self(Translator::transStatic('domain.user.invalid_address_type', replace: ['type' => $type]));
    }
}
