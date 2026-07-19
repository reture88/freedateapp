<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\SaveDatingProfile;

use App\Profile\DomainService\SaveDatingProfileOnboardingService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class SaveDatingProfileCommandHandler implements CommandHandlerInterface
{
    public function __construct(private SaveDatingProfileOnboardingService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): SaveDatingProfileCommandResponse
    {
        if (!$command instanceof SaveDatingProfileCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', SaveDatingProfileCommand::class));
        }

        $profile = $this->service->handle($command);

        return new SaveDatingProfileCommandResponse((int) $profile->getKey());
    }
}
