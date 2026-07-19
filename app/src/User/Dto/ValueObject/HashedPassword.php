<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

final readonly class HashedPassword
{
    public function __construct(private string $value)
    {
        if ('' === $value) {
            throw new \InvalidArgumentException('Password hash cannot be empty.');
        }
    }

    public static function fromPlainText(string $password): self
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password must contain at least 8 characters.');
        }

        return new self(password_hash($password, PASSWORD_DEFAULT));
    }

    public function verify(string $password): bool
    {
        return password_verify($password, $this->value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
