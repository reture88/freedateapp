<?php

declare(strict_types=1);

namespace App\User\RequestHandler\ChangePassword;

use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;
use App\User\DomainService\ChangePasswordService;

final readonly class ChangePasswordCommandHandler implements CommandHandlerInterface
{
    public function __construct(private ChangePasswordService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): ?CommandResponseInterface
    {
        if (!$command instanceof ChangePasswordCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', ChangePasswordCommand::class));
        }

        $this->service->handle(
            $command->user,
            $command->currentPassword,
            $command->password,
            $command->passwordConfirmation,
        );

        return null;
    }
}
