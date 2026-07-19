<?php

declare(strict_types=1);

namespace App\Shared\Dto\Query;

final readonly class Sort
{
    public function __construct(
        private string $field,
        private string $direction = 'asc',
    ) {
        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new \InvalidArgumentException('Sort direction must be asc or desc.');
        }
    }

    /**
     * @param list<string> $allowedFields
     */
    public static function fromInput(mixed $field, mixed $direction, array $allowedFields, string $defaultField): self
    {
        $field = is_string($field) && in_array($field, $allowedFields, true) ? $field : $defaultField;
        $direction = 'desc' === $direction ? 'desc' : 'asc';

        return new self($field, $direction);
    }

    public function field(): string
    {
        return $this->field;
    }

    public function direction(): string
    {
        return $this->direction;
    }
}
