<?php

declare(strict_types=1);

namespace App\Profile\DomainService;

use App\Models\ProfilePhoto;
use App\Models\User;
use App\Profile\RequestHandler\ChoosePrimaryProfilePhoto\ChoosePrimaryProfilePhotoCommand;
use Illuminate\Support\Facades\DB;

final class ChoosePrimaryProfilePhotoService
{
    public function handle(ChoosePrimaryProfilePhotoCommand $command): ProfilePhoto
    {
        $profile = User::query()->findOrFail($command->userId)->datingProfile()->firstOrFail();
        $photo = $profile->photos()->whereKey($command->photoId)->firstOrFail();

        DB::transaction(function () use ($profile, $photo): void {
            $profile->photos()->update(['is_primary' => false]);
            $photo->forceFill(['is_primary' => true])->save();
        });

        return $photo->fresh();
    }
}
