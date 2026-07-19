<?php

declare(strict_types=1);

namespace App\Shared\Dto\ValueObject;

final readonly class Money
{
    public function __construct(
        private int $minorAmount,
        private string $currency = 'HUF',
    ) {
        if ('' === $currency || 1 !== preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new \InvalidArgumentException('Currency must be a valid ISO 4217 code.');
        }
    }

    public static function fromMajor(int $amount, string $currency = 'HUF'): self
    {
        return new self($amount * 100, $currency);
    }

    public function minorAmount(): int
    {
        return $this->minorAmount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $money): self
    {
        $this->assertSameCurrency($money);

        return new self($this->minorAmount + $money->minorAmount, $this->currency);
    }

    public function subtract(self $money): self
    {
        $this->assertSameCurrency($money);

        return new self($this->minorAmount - $money->minorAmount, $this->currency);
    }

    public function multiply(int $quantity): self
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative.');
        }

        return new self($this->minorAmount * $quantity, $this->currency);
    }

    public function format(): string
    {
        return number_format($this->minorAmount / 100, 2, '.', ' ').' '.$this->currency;
    }

    private function assertSameCurrency(self $money): void
    {
        if ($this->currency !== $money->currency) {
            throw new \InvalidArgumentException('Cannot operate on different currencies.');
        }
    }
}
