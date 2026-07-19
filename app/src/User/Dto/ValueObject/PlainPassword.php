<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

final readonly class PlainPassword
{
    public function __construct(private string $value)
    {
        if (strlen($value) < 8) {
            throw new \InvalidArgumentException('Password must contain at least 8 characters.');
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
