<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\ChoosePrimaryProfilePhoto;

use App\Profile\DomainService\ChoosePrimaryProfilePhotoService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class ChoosePrimaryProfilePhotoCommandHandler implements CommandHandlerInterface
{
    public function __construct(private ChoosePrimaryProfilePhotoService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): ChoosePrimaryProfilePhotoCommandResponse
    {
        if (!$command instanceof ChoosePrimaryProfilePhotoCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', ChoosePrimaryProfilePhotoCommand::class));
        }

        $photo = $this->service->handle($command);

        return new ChoosePrimaryProfilePhotoCommandResponse((int) $photo->getKey());
    }
}
