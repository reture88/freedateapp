<?php

declare(strict_types=1);

namespace App\Shared\DomainService\Bus\RequestHandler\Exceptions;

use App\Shared\Infrastructure\Translator\Translator;
use RuntimeException;

final class MissingSelfConstructingRequestInterfaceException extends RuntimeException
{
    public static function create(string $class): self
    {
        return new self(Translator::transStatic('domain.shared.missing_self_constructing_request', replace: ['class' => $class]));
    }
}
