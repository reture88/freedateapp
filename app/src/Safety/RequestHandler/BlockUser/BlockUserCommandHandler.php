<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\BlockUser;

use App\Safety\DomainService\BlockUserService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class BlockUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private BlockUserService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): BlockUserCommandResponse
    {
        if (!$command instanceof BlockUserCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', BlockUserCommand::class));
        }

        $block = $this->service->handle($command);

        return new BlockUserCommandResponse((int) $block->getKey());
    }
}
