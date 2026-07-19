<?php

declare(strict_types=1);

namespace App\Shared\Dto\Pagination;

final readonly class Pagination
{
    public function __construct(
        private int $page = 1,
        private int $perPage = 20,
    ) {
        if ($page < 1) {
            throw new \InvalidArgumentException('Page must be greater than zero.');
        }

        if ($perPage < 1 || $perPage > 100) {
            throw new \InvalidArgumentException('Per page must be between 1 and 100.');
        }
    }

    public static function fromInput(mixed $page, mixed $perPage, int $defaultPerPage = 20): self
    {
        return new self(
            max(1, (int) ($page ?: 1)),
            min(100, max(1, (int) ($perPage ?: $defaultPerPage))),
        );
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
