<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Application;

use App\Shared\DomainService\Bus\InMemoryCommandBus;
use App\Shared\DomainService\Bus\InMemoryQueryBus;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use App\Shared\Infrastructure\Container\ContainerInterface;
use App\Shared\Infrastructure\Container\NativeContainer;
use App\Shared\Infrastructure\Validation\SimpleValidator;
use App\Shared\Infrastructure\Validation\ValidatorInterface;

final class ApplicationFactory
{
    public static function createContainer(): NativeContainer
    {
        $container = new NativeContainer();

        $container->instance(ContainerInterface::class, $container);
        $container->instance(ValidatorInterface::class, new SimpleValidator());
        $container->bind(CommandBusInterface::class, InMemoryCommandBus::class);
        $container->bind(QueryBusInterface::class, InMemoryQueryBus::class);

        return $container;
    }
}
