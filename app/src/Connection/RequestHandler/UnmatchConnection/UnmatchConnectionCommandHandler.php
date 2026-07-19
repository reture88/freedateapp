<?php

declare(strict_types=1);

namespace App\Connection\RequestHandler\UnmatchConnection;

use App\Connection\DomainService\UnmatchConnectionService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class UnmatchConnectionCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UnmatchConnectionService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): UnmatchConnectionCommandResponse
    {
        if (!$command instanceof UnmatchConnectionCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', UnmatchConnectionCommand::class));
        }

        $connection = $this->service->handle($command);

        return new UnmatchConnectionCommandResponse((int) $connection->getKey());
    }
}
