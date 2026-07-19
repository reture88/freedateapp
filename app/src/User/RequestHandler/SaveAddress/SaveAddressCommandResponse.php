<?php

declare(strict_types=1);

namespace App\User\RequestHandler\SaveAddress;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class SaveAddressCommandResponse implements CommandResponseInterface
{
    public function __construct(
        public string $addressId,
        public string $type,
    ) {
    }
}
