<?php

declare(strict_types=1);

namespace App\Shared\DomainService\Bus;

use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Exception\HandlerNotFoundException;
use App\Shared\Infrastructure\Bus\GenericBus;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use App\Shared\Infrastructure\Bus\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\QueryInterface;
use App\Shared\Infrastructure\Bus\Query\QueryResponseInterface;
use App\Shared\Infrastructure\Container\ContainerInterface;
use App\Shared\Infrastructure\Validation\ValidatorInterface;

final class InMemoryQueryBus extends GenericBus implements QueryBusInterface
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
    public function askPayload(string $class, array $payload, ContextInterface $context): QueryResponseInterface
    {
        $query = $this->create($class, $payload);

        if (!$query instanceof QueryInterface) {
            throw HandlerNotFoundException::forRequest($query);
        }

        return $this->ask($query, $context);
    }

    public function ask(QueryInterface $query, ContextInterface $context): QueryResponseInterface
    {
        return $this->getHandler($query)->handle($query, $context);
    }

    private function getHandler(QueryInterface $query): QueryHandlerInterface
    {
        $handlerClass = sprintf('%sHandler', $query::class);

        if (!class_exists($handlerClass) || !is_subclass_of($handlerClass, QueryHandlerInterface::class)) {
            throw HandlerNotFoundException::forRequest($query);
        }

        $handler = $this->container->get($handlerClass);

        if (!$handler instanceof QueryHandlerInterface) {
            throw HandlerNotFoundException::forRequest($query);
        }

        return $handler;
    }
}
