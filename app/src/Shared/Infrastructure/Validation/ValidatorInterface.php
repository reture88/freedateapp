<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Validation;

interface ValidatorInterface
{
    /**
     * @param array<string, mixed>  $payload
     * @param array<string, string> $rules
     */
    public function validate(array $payload, array $rules): void;
}
