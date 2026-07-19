<?php

declare(strict_types=1);

namespace App\Profile\DomainService;

use App\Models\DatingProfile;
use App\Models\User;
use Illuminate\Support\Carbon;

final class SaveDatingProfileOnboardingService
{
    /**
     * @param array<string, mixed> $data
     */
    public function handle(User $user, array $data): DatingProfile
    {
        $profile = $user->datingProfile()->updateOrCreate([], [
            'display_name' => $data['display_name'],
            'birthdate' => $data['birthdate'],
            'gender' => $data['gender'],
            'orientation' => $data['orientation'],
            'city' => $data['city'],
            'country_code' => strtoupper((string) $data['country_code']),
            'approximate_latitude' => $data['approximate_latitude'] ?? null,
            'approximate_longitude' => $data['approximate_longitude'] ?? null,
            'relationship_intent' => $data['relationship_intent'],
            'bio' => $data['bio'] ?? null,
            'visibility_status' => 'visible',
            'moderation_status' => 'pending',
            'ai_wingman_enabled' => (bool) ($data['ai_wingman_enabled'] ?? false),
            'completed_at' => Carbon::now(),
        ]);

        return $profile;
    }
}
