<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

final readonly class CountryCode
{
    public function __construct(private string $value)
    {
        if (1 !== preg_match('/^[A-Z]{2}$/', $value)) {
            throw new \InvalidArgumentException('Country code must be a valid ISO 3166-1 alpha-2 code.');
        }
    }

    public static function fromString(string $value): self
    {
        return new self(strtoupper($value));
    }

    public function value(): string
    {
        return $this->value;
    }
}
