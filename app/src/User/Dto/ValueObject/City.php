<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

final readonly class City
{
    public function __construct(private string $value)
    {
        if ('' === trim($value)) {
            throw new \InvalidArgumentException('City cannot be empty.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
