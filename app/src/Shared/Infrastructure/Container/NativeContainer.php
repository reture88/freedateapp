<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Container;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class NativeContainer implements ContainerInterface
{
    /**
     * @var array<string, callable(self): object|object|string>
     */
    private array $bindings = [];

    /**
     * @var array<string, object>
     */
    private array $instances = [];

    public function bind(string $abstract, callable|object|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function get(string $class): object
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        if (isset($this->bindings[$class])) {
            $binding = $this->bindings[$class];
            $object = match (true) {
                is_string($binding) => $this->get($binding),
                is_callable($binding) => $binding($this),
                default => $binding,
            };

            if (!is_object($object)) {
                throw new RuntimeException(sprintf('Container binding "%s" did not return an object.', $class));
            }

            return $object;
        }

        return $this->build($class);
    }

    private function build(string $class): object
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException(sprintf('Class "%s" is not instantiable.', $class));
        }

        $constructor = $reflection->getConstructor();

        if (null === $constructor) {
            return new $class();
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new RuntimeException(sprintf('Cannot resolve "%s::$%s".', $class, $parameter->getName()));
            }

            $arguments[] = $this->get($type->getName());
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
