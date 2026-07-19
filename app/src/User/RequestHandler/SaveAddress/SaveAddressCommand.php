<?php

declare(strict_types=1);

namespace App\User\RequestHandler\SaveAddress;

use App\Models\User;
use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\Dto\ValueObject\AddressData;
use App\User\Dto\ValueObject\AddressType;
use App\User\Dto\ValueObject\City;
use App\User\Dto\ValueObject\CountryCode;
use App\User\Dto\ValueObject\Name;
use App\User\Dto\ValueObject\PhoneNumber;
use App\User\Dto\ValueObject\PostalCode;
use App\User\Dto\ValueObject\StreetAddress;

final readonly class SaveAddressCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public User $user,
        public AddressData $addressData,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        if (!$payload['user'] instanceof User) {
            throw new \InvalidArgumentException('Authenticated user is required.');
        }

        return new self(
            $payload['user'],
            new AddressData(
                new AddressType((string) $payload['type']),
                new Name((string) $payload['name']),
                CountryCode::fromString((string) ($payload['country'] ?: 'HU')),
                new PostalCode((string) $payload['postal_code']),
                new City((string) $payload['city']),
                new StreetAddress((string) $payload['street']),
                isset($payload['phone']) && '' !== $payload['phone'] ? new PhoneNumber((string) $payload['phone']) : null,
            ),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'type' => 'required',
            'name' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
            'city' => 'required',
            'street' => 'required',
        ];
    }
}
