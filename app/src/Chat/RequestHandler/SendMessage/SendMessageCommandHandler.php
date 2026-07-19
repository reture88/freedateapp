<?php

declare(strict_types=1);

namespace App\Chat\RequestHandler\SendMessage;

use App\Chat\DomainService\SendMessageService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class SendMessageCommandHandler implements CommandHandlerInterface
{
    public function __construct(private SendMessageService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): SendMessageCommandResponse
    {
        if (!$command instanceof SendMessageCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', SendMessageCommand::class));
        }

        $message = $this->service->handle($command);

        return new SendMessageCommandResponse((int) $message->getKey());
    }
}
