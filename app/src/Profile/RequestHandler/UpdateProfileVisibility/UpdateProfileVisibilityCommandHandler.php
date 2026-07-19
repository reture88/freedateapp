<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\UpdateProfileVisibility;

use App\Profile\DomainService\UpdateDatingProfileVisibilityService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class UpdateProfileVisibilityCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UpdateDatingProfileVisibilityService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): UpdateProfileVisibilityCommandResponse
    {
        if (!$command instanceof UpdateProfileVisibilityCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', UpdateProfileVisibilityCommand::class));
        }

        $profile = $this->service->handle($command);

        return new UpdateProfileVisibilityCommandResponse((int) $profile->getKey(), $profile->visibility_status);
    }
}
