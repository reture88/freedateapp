<?php

declare(strict_types=1);

namespace App\Discovery\RequestHandler\RecordDiscoveryDecision;

use App\Discovery\DomainService\RecordDiscoveryDecisionService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class RecordDiscoveryDecisionCommandHandler implements CommandHandlerInterface
{
    public function __construct(private RecordDiscoveryDecisionService $service)
    {
    }

    public function handle(CommandInterface $command, ContextInterface $context): RecordDiscoveryDecisionCommandResponse
    {
        if (!$command instanceof RecordDiscoveryDecisionCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', RecordDiscoveryDecisionCommand::class));
        }

        $decision = $this->service->handle($command);

        return new RecordDiscoveryDecisionCommandResponse((int) $decision->getKey());
    }
}
