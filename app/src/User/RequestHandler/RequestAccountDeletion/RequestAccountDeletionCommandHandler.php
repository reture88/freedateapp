<?php

declare(strict_types=1);

namespace App\User\RequestHandler\RequestAccountDeletion;

use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\User\DomainService\RequestAccountDeletionService;

final readonly class RequestAccountDeletionCommandHandler implements CommandHandlerInterface
{
    public function __construct(private RequestAccountDeletionService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): RequestAccountDeletionCommandResponse
    {
        if (!$command instanceof RequestAccountDeletionCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', RequestAccountDeletionCommand::class));
        }

        $user = $this->service->handle($command);

        return new RequestAccountDeletionCommandResponse((int) $user->getKey());
    }
}
