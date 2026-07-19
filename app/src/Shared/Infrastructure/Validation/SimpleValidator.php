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
                if ('nullable' === $rule && (null === $value || '' === $value)) {
                    continue 2;
                }

                if ('required' === $rule && (null === $value || '' === $value || [] === $value)) {
                    $errors[$field][] = 'The field is required.';
                }

                if ('email' === $rule && null !== $value && false === filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'The field must be a valid email address.';
                }

                if ('accepted' === $rule && !in_array($value, ['1', 1, true, 'true', 'on', 'yes'], true)) {
                    $errors[$field][] = 'The field must be accepted.';
                }

                if ('boolean' === $rule && null !== $value && !in_array($value, ['0', '1', 0, 1, true, false], true)) {
                    $errors[$field][] = 'The field must be true or false.';
                }

                if ('numeric' === $rule && null !== $value && '' !== $value && !is_numeric($value)) {
                    $errors[$field][] = 'The field must be numeric.';
                }

                if ('array' === $rule && null !== $value && !is_array($value)) {
                    $errors[$field][] = 'The field must be an array.';
                }

                if ('date' === $rule && null !== $value && false === strtotime((string) $value)) {
                    $errors[$field][] = 'The field must be a valid date.';
                }

                if (str_starts_with($rule, 'max:') && null !== $value && strlen((string) $value) > (int) substr($rule, 4)) {
                    $errors[$field][] = sprintf('The field must not exceed %d characters.', (int) substr($rule, 4));
                }

                if (str_starts_with($rule, 'size:') && null !== $value && strlen((string) $value) !== (int) substr($rule, 5)) {
                    $errors[$field][] = sprintf('The field must be %d characters.', (int) substr($rule, 5));
                }

                if (str_starts_with($rule, 'in:') && null !== $value) {
                    $allowed = explode(',', substr($rule, 3));

                    if (!in_array((string) $value, $allowed, true)) {
                        $errors[$field][] = 'The selected value is invalid.';
                    }
                }

                if (str_starts_with($rule, 'between:') && null !== $value && '' !== $value && is_numeric($value)) {
                    [$minimum, $maximum] = array_map('floatval', explode(',', substr($rule, 8), 2));
                    $numericValue = (float) $value;

                    if ($numericValue < $minimum || $numericValue > $maximum) {
                        $errors[$field][] = sprintf('The field must be between %s and %s.', $minimum, $maximum);
                    }
                }

                if (str_starts_with($rule, 'min:') && null !== $value && '' !== $value && is_numeric($value)) {
                    $minimum = (float) substr($rule, 4);

                    if ((float) $value < $minimum) {
                        $errors[$field][] = sprintf('The field must be at least %s.', $minimum);
                    }
                }

                if (str_starts_with($rule, 'max_value:') && null !== $value && '' !== $value && is_numeric($value)) {
                    $maximum = (float) substr($rule, 10);

                    if ((float) $value > $maximum) {
                        $errors[$field][] = sprintf('The field must not be greater than %s.', $maximum);
                    }
                }

                if (str_starts_with($rule, 'min_items:') && is_array($value)) {
                    $minimum = (int) substr($rule, 10);

                    if (count($value) < $minimum) {
                        $errors[$field][] = sprintf('The field must contain at least %d items.', $minimum);
                    }
                }

                if (str_starts_with($rule, 'lte:') && null !== $value && '' !== $value && is_numeric($value)) {
                    $otherField = substr($rule, 4);
                    $otherValue = $payload[$otherField] ?? null;

                    if (is_numeric($otherValue) && (float) $value > (float) $otherValue) {
                        $errors[$field][] = sprintf('The field must be less than or equal to %s.', $otherField);
                    }
                }

                $valueTimestamp = false;

                if ('adult' === $rule && null !== $value && false !== ($valueTimestamp = strtotime((string) $value))) {
                    $adultThreshold = strtotime('-18 years');

                    if ($valueTimestamp > $adultThreshold) {
                        $errors[$field][] = 'User must be at least 18 years old.';
                    }
                }
            }
        }

        if ([] !== $errors) {
            throw new ValidationException($errors);
        }
    }
}
