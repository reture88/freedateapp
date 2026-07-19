<?php

declare(strict_types=1);

namespace App\User\Dto\ValueObject;

final readonly class AddressData
{
    public function __construct(
        public AddressType $type,
        public Name $name,
        public CountryCode $country,
        public PostalCode $postalCode,
        public City $city,
        public StreetAddress $street,
        public ?PhoneNumber $phone = null,
    ) {
    }
}
