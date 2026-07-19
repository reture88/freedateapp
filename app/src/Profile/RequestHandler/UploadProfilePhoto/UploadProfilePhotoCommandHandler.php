<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\UploadProfilePhoto;

use App\Profile\DomainService\UploadProfilePhotoService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class UploadProfilePhotoCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UploadProfilePhotoService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): UploadProfilePhotoCommandResponse
    {
        if (!$command instanceof UploadProfilePhotoCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', UploadProfilePhotoCommand::class));
        }

        $photo = $this->service->handle($command);

        return new UploadProfilePhotoCommandResponse((int) $photo->getKey());
    }
}
