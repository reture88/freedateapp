<?php

declare(strict_types=1);

namespace App\Profile\DomainService;

use App\Models\DatingProfile;
use App\Models\User;
use App\Preference\DomainService\EnsureDefaultPreferenceService;
use App\Profile\RequestHandler\SaveDatingProfile\SaveDatingProfileCommand;
use Illuminate\Support\Carbon;

final class SaveDatingProfileOnboardingService
{
    public function __construct(private EnsureDefaultPreferenceService $defaultPreferenceService)
    {
    }

    public function handle(SaveDatingProfileCommand $command): DatingProfile
    {
        $user = User::query()->findOrFail($command->userId);

        $profile = $user->datingProfile()->updateOrCreate([], [
            'display_name' => $command->displayName,
            'birthdate' => $command->birthdate,
            'gender' => $command->gender,
            'orientation' => $command->orientation,
            'city' => $command->city,
            'country_code' => $command->countryCode,
            'approximate_latitude' => $command->approximateLatitude,
            'approximate_longitude' => $command->approximateLongitude,
            'relationship_intent' => $command->relationshipIntent,
            'bio' => $command->bio,
            'visibility_status' => 'visible',
            'moderation_status' => 'pending',
            'ai_wingman_enabled' => $command->aiWingmanEnabled,
            'completed_at' => Carbon::now(),
        ]);

        $this->defaultPreferenceService->handle($profile);
        $this->syncTags($profile, $command->profileTags);

        return $profile;
    }

    /**
     * @param array<int, string> $profileTags
     */
    private function syncTags(DatingProfile $profile, array $profileTags): void
    {
        $syncPayload = [];
        $sortOrder = 0;

        foreach ($profileTags as $tagId => $visibility) {
            $syncPayload[$tagId] = [
                'visibility' => $visibility,
                'sort_order' => $sortOrder++,
            ];
        }

        $profile->tags()->sync($syncPayload);
    }
}
