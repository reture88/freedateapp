<?php

declare(strict_types=1);

namespace App\User\RequestHandler\UpdateProfile;

use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\DomainService\UpdateProfileService;

final readonly class UpdateProfileCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UpdateProfileService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): UpdateProfileCommandResponse
    {
        if (!$command instanceof UpdateProfileCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', UpdateProfileCommand::class));
        }

        $user = $this->service->handle($command->user, $command->name, $command->email);

        return new UpdateProfileCommandResponse((string) $user->getAuthIdentifier(), (string) $user->email);
    }
}
