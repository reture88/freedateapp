<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

final readonly class PostalCode
{
    public function __construct(private string $value)
    {
        if ('' === trim($value)) {
            throw new \InvalidArgumentException('Postal code cannot be empty.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
