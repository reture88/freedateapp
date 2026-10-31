<?php

declare(strict_types=1);

namespace App\Profile\Support;

use Carbon\CarbonImmutable;
use DateTimeInterface;

final class AdultEligibility
{
    public const MINIMUM_AGE = 18;
    public const MESSAGE = 'You must be at least 18 years old to use MTAM.';

    public static function isEligible(mixed $birthdate, ?CarbonImmutable $today = null): bool
    {
        $date = self::birthdate($birthdate);

        if (!$date instanceof CarbonImmutable) {
            return false;
        }

        return $date->startOfDay()->lessThanOrEqualTo(self::latestEligibleBirthdate($today));
    }

    public static function latestEligibleBirthdate(?CarbonImmutable $today = null): CarbonImmutable
    {
        return ($today ?? CarbonImmutable::today())->subYears(self::MINIMUM_AGE)->startOfDay();
    }

    private static function birthdate(mixed $birthdate): ?CarbonImmutable
    {
        if ($birthdate instanceof DateTimeInterface) {
            return CarbonImmutable::instance($birthdate)->startOfDay();
        }

        if (!is_string($birthdate) || '' === trim($birthdate)) {
            return null;
        }

        try {
            $date = CarbonImmutable::createFromFormat('Y-m-d', trim($birthdate));
        } catch (\Throwable) {
            return null;
        }

        if (!$date instanceof CarbonImmutable) {
            return null;
        }

        return $date->format('Y-m-d') === trim($birthdate) ? $date->startOfDay() : null;
    }
}
