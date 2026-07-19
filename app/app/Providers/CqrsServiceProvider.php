<?php

declare(strict_types=1);

namespace App\Providers;

use App\Shared\DomainService\Bus\InMemoryCommandBus;
use App\Shared\DomainService\Bus\InMemoryQueryBus;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use App\Shared\Infrastructure\Container\ContainerInterface;
use App\Shared\Infrastructure\Container\LaravelContainerAdapter;
use App\Shared\Infrastructure\Validation\SimpleValidator;
use App\Shared\Infrastructure\Validation\ValidatorInterface;
use Illuminate\Support\ServiceProvider;

final class CqrsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ContainerInterface::class, fn () => new LaravelContainerAdapter($this->app));
        $this->app->singleton(ValidatorInterface::class, SimpleValidator::class);
        $this->app->bind(CommandBusInterface::class, InMemoryCommandBus::class);
        $this->app->bind(QueryBusInterface::class, InMemoryQueryBus::class);
    }
}
