<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Validation;

use InvalidArgumentException;

final class ValidationException extends InvalidArgumentException
{
    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct('Validation failed.');
    }

    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
