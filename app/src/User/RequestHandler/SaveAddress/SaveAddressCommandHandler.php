<?php

declare(strict_types=1);

namespace App\User\RequestHandler\SaveAddress;

use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\DomainService\SaveAddressService;

final readonly class SaveAddressCommandHandler implements CommandHandlerInterface
{
    public function __construct(private SaveAddressService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): SaveAddressCommandResponse
    {
        if (!$command instanceof SaveAddressCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', SaveAddressCommand::class));
        }

        $address = $this->service->handle(
            $command->user,
            $command->addressData,
        );

        return new SaveAddressCommandResponse((string) $address->getKey(), (string) $address->type);
    }
}
