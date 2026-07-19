<?php

declare(strict_types=1);

namespace App\User\RequestHandler\DeactivateAccount;

use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\DomainService\DeactivateAccountService;

final readonly class DeactivateAccountCommandHandler implements CommandHandlerInterface
{
    public function __construct(private DeactivateAccountService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): DeactivateAccountCommandResponse
    {
        if (!$command instanceof DeactivateAccountCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', DeactivateAccountCommand::class));
        }

        $user = $this->service->handle($command);

        return new DeactivateAccountCommandResponse((int) $user->getKey());
    }
}
