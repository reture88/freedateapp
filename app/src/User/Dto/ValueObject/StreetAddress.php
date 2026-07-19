<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

final readonly class StreetAddress
{
    public function __construct(private string $value)
    {
        if ('' === trim($value)) {
            throw new \InvalidArgumentException('Street address cannot be empty.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
