<?php

declare(strict_types=1);

namespace App\Preference\RequestHandler\SavePreference;

use App\Preference\DomainService\SavePreferenceService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class SavePreferenceCommandHandler implements CommandHandlerInterface
{
    public function __construct(private SavePreferenceService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): SavePreferenceCommandResponse
    {
        if (!$command instanceof SavePreferenceCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', SavePreferenceCommand::class));
        }

        $preference = $this->service->handle($command);

        return new SavePreferenceCommandResponse((int) $preference->getKey());
    }
}
