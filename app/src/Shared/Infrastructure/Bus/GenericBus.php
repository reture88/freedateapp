<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\DomainService\Bus\RequestHandler\Exceptions\MissingSelfConstructingRequestInterfaceException;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\Shared\Infrastructure\Bus\Query\QueryInterface;
use App\Shared\Infrastructure\Validation\ValidatorInterface;

abstract class GenericBus
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws MissingSelfConstructingRequestInterfaceException
     */
    protected function create(string $class, array $payload): CommandInterface|QueryInterface
    {
        if (is_subclass_of($class, ValidateRequestInterface::class)) {
            $this->validator->validate($payload, $class::rules());
        }

        if (is_callable([$class, 'createFromArray'], true)) {
            return $class::createFromArray($payload);
        }

        throw MissingSelfConstructingRequestInterfaceException::create($class);
    }
}
