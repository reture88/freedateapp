<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\User;
use App\Models\UserAddress;
use App\User\Dto\ValueObject\AddressData;

final readonly class SaveAddressService
{
    public function handle(
        User $user,
        AddressData $addressData,
    ): UserAddress {
        /** @var UserAddress $address */
        $address = UserAddress::query()->updateOrCreate(
            [
                'user_id' => $user->getKey(),
                'type' => $addressData->type->value(),
            ],
            [
                'name' => $addressData->name->value(),
                'country' => $addressData->country->value(),
                'postal_code' => $addressData->postalCode->value(),
                'city' => $addressData->city->value(),
                'street' => $addressData->street->value(),
                'phone' => $addressData->phone?->value(),
            ],
        );

        return $address;
    }
}
