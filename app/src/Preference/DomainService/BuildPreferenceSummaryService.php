<?php

declare(strict_types=1);

namespace App\Preference\DomainService;

use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Preference\Dto\PreferenceSummary;

final class BuildPreferenceSummaryService
{
    public function __construct(private ?EnsureDefaultPreferenceService $defaultPreferenceService = null)
    {
    }

    public function handle(DatingProfile $profile): PreferenceSummary
    {
        $preference = $this->preferenceFor($profile);

        return new PreferenceSummary([
            'age_min' => $preference->preferred_age_min,
            'age_max' => $preference->preferred_age_max,
            'distance_km' => $preference->preferred_distance_km,
            'genders' => array_values($preference->preferred_genders),
            'orientations' => array_values($preference->preferred_orientations),
            'relationship_intents' => array_values($preference->relationship_intents),
            'exclude_ai_wingman_enabled' => $preference->exclude_ai_wingman_enabled,
        ], sprintf(
            'Ages %d-%d, within %d km, looking for %s',
            $preference->preferred_age_min,
            $preference->preferred_age_max,
            $preference->preferred_distance_km,
            $this->humanIntent((string) $preference->relationship_intents[0]),
        ));
    }

    private function preferenceFor(DatingProfile $profile): ProfilePreference
    {
        if ($this->defaultPreferenceService instanceof EnsureDefaultPreferenceService) {
            return $this->defaultPreferenceService->handle($profile);
        }

        return $profile->preference()->firstOrFail();
    }

    private function humanIntent(string $intent): string
    {
        return match ($intent) {
            'long_term' => 'long-term relationship',
            'short_term' => 'short-term dating',
            'friendship' => 'friendship first',
            default => 'not sure yet',
        };
    }
}
