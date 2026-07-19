<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\DatingProfile;
use App\Models\ProfilePhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ProfilePhotoManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_profile_photo(): void
    {
        Storage::fake('public');
        [$user, $profile] = $this->createUserWithProfile();

        $this->actingAs($user)
            ->post('/dating-profile/photos', [
                'photo' => UploadedFile::fake()->create('profile.jpg', 512, 'image/jpeg'),
            ])
            ->assertRedirect('/dating-profile/edit');

        $photo = ProfilePhoto::query()->whereBelongsTo($profile)->firstOrFail();

        $this->assertTrue($photo->is_primary);
        $this->assertSame('approved', $photo->moderation_status);
        Storage::disk('public')->assertExists($photo->path);
    }

    public function test_user_cannot_upload_more_than_seven_profile_photos(): void
    {
        Storage::fake('public');
        [$user, $profile] = $this->createUserWithProfile();

        for ($index = 0; $index < 7; ++$index) {
            ProfilePhoto::query()->create([
                'dating_profile_id' => $profile->getKey(),
                'path' => sprintf('profile-photos/existing-%d.jpg', $index),
                'sort_order' => $index,
                'is_primary' => 0 === $index,
                'moderation_status' => 'approved',
            ]);
        }

        $this->actingAs($user)
            ->from('/dating-profile/edit')
            ->post('/dating-profile/photos', [
                'photo' => UploadedFile::fake()->create('extra.jpg', 512, 'image/jpeg'),
            ])
            ->assertRedirect('/dating-profile/edit')
            ->assertSessionHasErrors(['photo']);

        $this->assertSame(7, ProfilePhoto::query()->whereBelongsTo($profile)->count());
    }

    public function test_user_can_choose_primary_profile_photo(): void
    {
        [$user, $profile] = $this->createUserWithProfile();
        $oldPrimary = ProfilePhoto::query()->create([
            'dating_profile_id' => $profile->getKey(),
            'path' => 'profile-photos/old.jpg',
            'sort_order' => 0,
            'is_primary' => true,
            'moderation_status' => 'approved',
        ]);
        $newPrimary = ProfilePhoto::query()->create([
            'dating_profile_id' => $profile->getKey(),
            'path' => 'profile-photos/new.jpg',
            'sort_order' => 1,
            'is_primary' => false,
            'moderation_status' => 'approved',
        ]);

        $this->actingAs($user)
            ->post(sprintf('/dating-profile/photos/%d/primary', $newPrimary->getKey()))
            ->assertRedirect('/dating-profile/edit');

        $this->assertFalse($oldPrimary->fresh()->is_primary);
        $this->assertTrue($newPrimary->fresh()->is_primary);
    }

    /**
     * @return array{User, DatingProfile}
     */
    private function createUserWithProfile(): array
    {
        $user = User::query()->create([
            'name' => 'Photo User',
            'email' => 'photo.user@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $profile = DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Photo User',
            'birthdate' => '1992-01-01',
            'gender' => 'woman',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-07-19 12:00:00',
        ]);

        return [$user, $profile];
    }
}
