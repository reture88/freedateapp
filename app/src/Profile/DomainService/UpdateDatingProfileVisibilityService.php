<?php

declare(strict_types=1);

namespace App\Profile\DomainService;

use App\Models\DatingProfile;
use App\Models\User;

final class UpdateDatingProfileVisibilityService
{
    public function handle(User $user, string $visibilityStatus): DatingProfile
    {
        $profile = $user->datingProfile()->firstOrFail();
        $profile->forceFill(['visibility_status' => $visibilityStatus])->save();

        return $profile;
    }
}
