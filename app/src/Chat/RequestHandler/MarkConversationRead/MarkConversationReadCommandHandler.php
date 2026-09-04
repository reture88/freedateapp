<?php

declare(strict_types=1);

namespace App\Chat\RequestHandler\MarkConversationRead;

use App\Chat\DomainService\MarkConversationReadService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class MarkConversationReadCommandHandler implements CommandHandlerInterface
{
    public function __construct(private MarkConversationReadService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): MarkConversationReadCommandResponse
    {
        if (!$command instanceof MarkConversationReadCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', MarkConversationReadCommand::class));
        }

        return new MarkConversationReadCommandResponse($this->service->handle($command));
    }
}
