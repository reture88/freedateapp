<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Container;

use Illuminate\Contracts\Container\Container;

final readonly class LaravelContainerAdapter implements ContainerInterface
{
    public function __construct(private Container $container)
    {
    }

    public function get(string $class): object
    {
        $object = $this->container->make($class);

        if (!is_object($object)) {
            throw new \RuntimeException(sprintf('Container did not return an object for "%s".', $class));
        }

        return $object;
    }
}
