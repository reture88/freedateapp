<?php

declare(strict_types=1);

namespace App\Shared\DomainService\Bus;

use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;
use App\Shared\Infrastructure\Bus\Exception\HandlerNotFoundException;
use App\Shared\Infrastructure\Bus\GenericBus;
use App\Shared\Infrastructure\Container\ContainerInterface;
use App\Shared\Infrastructure\Validation\ValidatorInterface;

final class InMemoryCommandBus extends GenericBus implements CommandBusInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        ValidatorInterface $validator,
    ) {
        parent::__construct($validator);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function dispatchPayload(string $class, array $payload, ContextInterface $context): ?CommandResponseInterface
    {
        $command = $this->create($class, $payload);

        if (!$command instanceof CommandInterface) {
            throw HandlerNotFoundException::forRequest($command);
        }

        return $this->dispatch($command, $context);
    }

    public function dispatch(CommandInterface $command, ContextInterface $context): ?CommandResponseInterface
    {
        return $this->getHandler($command)->handle($command, $context);
    }

    private function getHandler(CommandInterface $command): CommandHandlerInterface
    {
        $handlerClass = sprintf('%sHandler', $command::class);

        if (!class_exists($handlerClass) || !is_subclass_of($handlerClass, CommandHandlerInterface::class)) {
            throw HandlerNotFoundException::forRequest($command);
        }

        $handler = $this->container->get($handlerClass);

        if (!$handler instanceof CommandHandlerInterface) {
            throw HandlerNotFoundException::forRequest($command);
        }

        return $handler;
    }
}
