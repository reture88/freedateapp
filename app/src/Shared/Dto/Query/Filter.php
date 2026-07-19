<?php

declare(strict_types=1);

namespace App\Shared\Dto\Query;

final readonly class Filter
{
    /**
     * @param array<string, mixed> $criteria
     */
    public function __construct(private array $criteria = [])
    {
    }

    /**
     * @param array<string, mixed> $input
     * @param list<string>         $allowedKeys
     */
    public static function fromInput(array $input, array $allowedKeys): self
    {
        return new self(array_intersect_key($input, array_flip($allowedKeys)));
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->criteria) && null !== $this->criteria[$key] && '' !== $this->criteria[$key];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->criteria[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->criteria;
    }
}
