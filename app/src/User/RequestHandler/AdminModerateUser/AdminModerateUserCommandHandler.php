<?php

declare(strict_types=1);

namespace App\User\RequestHandler\AdminModerateUser;

use App\Models\User;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\DomainService\AdminModerateUserService;

final readonly class AdminModerateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private AdminModerateUserService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): AdminModerateUserCommandResponse
    {
        if (!$command instanceof AdminModerateUserCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', AdminModerateUserCommand::class));
        }

        $actor = User::query()->findOrFail($command->actorUserId);
        $target = User::query()->findOrFail($command->targetUserId);
        $moderatedUser = $this->service->handle($actor, $target, $command->action);

        return new AdminModerateUserCommandResponse((int) $moderatedUser->getKey());
    }
}
