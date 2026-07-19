<?php

declare(strict_types=1);

namespace App\Preference\DomainService;

use App\Models\DatingProfile;
use App\Models\ProfilePreference;

final class EnsureDefaultPreferenceService
{
    public function handle(DatingProfile $profile): ProfilePreference
    {
        $preference = $profile->preference()->first();

        if ($preference instanceof ProfilePreference) {
            return $preference;
        }

        return $profile->preference()->create([
            'preferred_age_min' => 18,
            'preferred_age_max' => 80,
            'preferred_distance_km' => 50,
            'preferred_genders' => ['woman', 'man', 'non_binary', 'other'],
            'preferred_orientations' => ['straight', 'gay', 'lesbian', 'bisexual', 'pansexual', 'queer', 'other'],
            'relationship_intents' => ['long_term', 'short_term', 'friendship', 'unsure'],
            'exclude_ai_wingman_enabled' => false,
            'dealbreakers' => [],
            'soft_preferences' => [],
        ]);
    }
}
