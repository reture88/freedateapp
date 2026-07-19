<?php

declare(strict_types=1);

namespace App\Shared\Dto\ValueObject;

use Illuminate\Support\Str;

final readonly class Slug
{
    public function __construct(private string $value)
    {
        if ('' === $value || 1 !== preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new \InvalidArgumentException('Invalid slug.');
        }
    }

    public static function fromString(string $value): self
    {
        $slug = Str::slug($value);

        if ('' === $slug) {
            throw new \InvalidArgumentException('Cannot create slug from empty value.');
        }

        return new self($slug);
    }

    public function value(): string
    {
        return $this->value;
    }
}
