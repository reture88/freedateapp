<?php

declare(strict_types=1);

namespace App\Profile\DomainService;

use App\Models\Connection;
use App\Models\DatingProfile;
use App\Models\User;
use App\Profile\RequestHandler\ShowMatchedProfile\ShowMatchedProfileQuery;

final class ShowMatchedProfileService
{
    public function handle(ShowMatchedProfileQuery $query): DatingProfile
    {
        $viewerProfile = User::query()->findOrFail($query->userId)->datingProfile()->firstOrFail();
        $profile = DatingProfile::query()->with(['photos', 'tags.category', 'user'])->findOrFail($query->profileId);

        if ((int) $viewerProfile->getKey() === (int) $profile->getKey()) {
            return $profile;
        }

        $isMatched = Connection::query()
            ->where('status', 'matched')
            ->where(static function ($builder) use ($viewerProfile, $profile): void {
                $builder
                    ->where(static function ($nested) use ($viewerProfile, $profile): void {
                        $nested
                            ->where('profile_one_id', $viewerProfile->getKey())
                            ->where('profile_two_id', $profile->getKey());
                    })
                    ->orWhere(static function ($nested) use ($viewerProfile, $profile): void {
                        $nested
                            ->where('profile_one_id', $profile->getKey())
                            ->where('profile_two_id', $viewerProfile->getKey());
                    });
            })
            ->exists();

        if (!$isMatched || !$profile->user->is_active || null !== $profile->user->deletion_requested_at) {
            throw new \InvalidArgumentException('Profile is not available.');
        }

        return $profile;
    }
}
