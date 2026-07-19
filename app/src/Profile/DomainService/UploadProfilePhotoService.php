<?php

declare(strict_types=1);

namespace App\Profile\DomainService;

use App\Models\ProfilePhoto;
use App\Models\User;
use App\Profile\RequestHandler\UploadProfilePhoto\UploadProfilePhotoCommand;
use App\Shared\Infrastructure\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

final class UploadProfilePhotoService
{
    private const MAX_PHOTOS = 7;

    public function handle(UploadProfilePhotoCommand $command): ProfilePhoto
    {
        $profile = User::query()->findOrFail($command->userId)->datingProfile()->firstOrFail();

        if ($profile->photos()->count() >= self::MAX_PHOTOS) {
            throw new ValidationException(['photo' => ['A profile can have at most 7 photos.']]);
        }

        return DB::transaction(function () use ($command, $profile): ProfilePhoto {
            $isFirstPhoto = 0 === $profile->photos()->count();
            $path = $command->photo->store('profile-photos', 'public');

            if (false === $path) {
                throw new \RuntimeException('Profile photo upload failed.');
            }

            if ($isFirstPhoto) {
                $profile->photos()->update(['is_primary' => false]);
            }

            /** @var ProfilePhoto $photo */
            $photo = $profile->photos()->create([
                'path' => $path,
                'sort_order' => (int) $profile->photos()->max('sort_order') + 1,
                'is_primary' => $isFirstPhoto,
                'moderation_status' => 'approved',
            ]);

            return $photo;
        });
    }
}
