<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Validation;

final class SimpleValidator implements ValidatorInterface
{
    /**
     * @param array<string, mixed>  $payload
     * @param array<string, string> $rules
     */
    public function validate(array $payload, array $rules): void
    {
        $errors = [];

        foreach ($rules as $field => $ruleList) {
            $rulesForField = explode('|', $ruleList);
            $value = $payload[$field] ?? null;

            foreach ($rulesForField as $rule) {
                if ('required' === $rule && (null === $value || '' === $value)) {
                    $errors[$field][] = 'The field is required.';
                }

                if ('email' === $rule && null !== $value && false === filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'The field must be a valid email address.';
                }
            }
        }

        if ([] !== $errors) {
            throw new ValidationException($errors);
        }
    }
}
