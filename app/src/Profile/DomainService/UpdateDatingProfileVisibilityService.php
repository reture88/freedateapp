<?php

declare(strict_types=1);

namespace App\Profile\DomainService;

use App\Models\DatingProfile;
use App\Models\User;
use App\Profile\RequestHandler\UpdateProfileVisibility\UpdateProfileVisibilityCommand;

final class UpdateDatingProfileVisibilityService
{
    public function handle(UpdateProfileVisibilityCommand $command): DatingProfile
    {
        $user = User::query()->findOrFail($command->userId);
        $profile = $user->datingProfile()->firstOrFail();
        $profile->forceFill(['visibility_status' => $command->visibilityStatus])->save();

        return $profile;
    }
}
