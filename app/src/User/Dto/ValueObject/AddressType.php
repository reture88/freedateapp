<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

use App\Models\UserAddress;
use App\User\DomainService\Exception\InvalidAddressTypeException;

final readonly class AddressType
{
    public function __construct(private string $value)
    {
        if (!in_array($value, UserAddress::allowedTypes(), true)) {
            throw InvalidAddressTypeException::create($value);
        }
    }

    public function value(): string
    {
        return $this->value;
    }
}
