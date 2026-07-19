<?php

declare(strict_types=1);

namespace App\Preference\Dto;

final readonly class PreferenceSummary
{
    /**
     * @param array{
     *     age_min: int,
     *     age_max: int,
     *     distance_km: int,
     *     genders: list<string>,
     *     orientations: list<string>,
     *     relationship_intents: list<string>,
     *     exclude_ai_wingman_enabled: bool
     * } $hardFilters
     */
    public function __construct(
        public array $hardFilters,
        public string $displayText,
    ) {
    }
}
