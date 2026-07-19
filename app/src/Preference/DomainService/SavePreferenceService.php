<?php

declare(strict_types=1);

namespace App\Preference\DomainService;

use App\Models\ProfilePreference;
use App\Models\User;
use App\Preference\RequestHandler\SavePreference\SavePreferenceCommand;

final class SavePreferenceService
{
    public function __construct(private EnsureDefaultPreferenceService $defaultPreferenceService)
    {
    }

    public function handle(SavePreferenceCommand $command): ProfilePreference
    {
        $profile = User::query()->findOrFail($command->userId)->datingProfile()->firstOrFail();
        $preference = $this->defaultPreferenceService->handle($profile);

        $preference->forceFill([
            'preferred_age_min' => $command->preferredAgeMin,
            'preferred_age_max' => $command->preferredAgeMax,
            'preferred_distance_km' => $command->preferredDistanceKm,
            'preferred_genders' => $command->preferredGenders,
            'preferred_orientations' => $command->preferredOrientations,
            'relationship_intents' => $command->relationshipIntents,
            'exclude_ai_wingman_enabled' => $command->excludeAiWingmanEnabled,
            'dealbreakers' => $command->dealbreakers,
            'soft_preferences' => $command->softPreferences,
        ])->save();

        return $preference;
    }
}
