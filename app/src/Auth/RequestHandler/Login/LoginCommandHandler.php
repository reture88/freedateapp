<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Login;

use App\Auth\DomainService\LoginService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class LoginCommandHandler implements CommandHandlerInterface
{
    public function __construct(private LoginService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): LoginCommandResponse
    {
        if (!$command instanceof LoginCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', LoginCommand::class));
        }

        $user = $this->service->handle($command->email, $command->password, $command->remember);

        return new LoginCommandResponse((string) $user->getAuthIdentifier(), (string) $user->email);
    }
}
