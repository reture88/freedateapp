<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Register;

use App\Auth\DomainService\RegisterUserService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class RegisterCommandHandler implements CommandHandlerInterface
{
    public function __construct(private RegisterUserService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): RegisterCommandResponse
    {
        if (!$command instanceof RegisterCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', RegisterCommand::class));
        }

        $user = $this->service->handle(
            $command->name,
            $command->email,
            $command->password,
            $command->passwordConfirmation,
        );

        return new RegisterCommandResponse((string) $user->getAuthIdentifier(), (string) $user->email);
    }
}
