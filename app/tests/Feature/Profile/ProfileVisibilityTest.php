<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\DatingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProfileVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_edit_completed_dating_profile(): void
    {
        [$user, $profile] = $this->createUserWithProfile();

        $this->actingAs($user)
            ->get('/dating-profile/edit')
            ->assertOk()
            ->assertSee('Edit dating profile')
            ->assertSee('Sanyi');

        $this->actingAs($user)
            ->post('/dating-profile/edit', [
                'display_name' => 'Sanyi Updated',
                'birthdate' => '1990-01-01',
                'gender' => 'man',
                'orientation' => 'straight',
                'city' => 'Pecs',
                'country_code' => 'HU',
                'approximate_latitude' => '46.072700',
                'approximate_longitude' => '18.232300',
                'relationship_intent' => 'long_term',
                'bio' => 'Updated bio.',
                'ai_wingman_enabled' => '0',
                'privacy_consent' => '1',
            ])
            ->assertRedirect('/dating-profile/preview');

        $profile->refresh();

        $this->assertSame('Sanyi Updated', $profile->display_name);
        $this->assertSame('Pecs', $profile->city);
        $this->assertFalse($profile->ai_wingman_enabled);
    }

    public function test_user_can_hide_and_show_dating_profile(): void
    {
        [$user, $profile] = $this->createUserWithProfile();

        $this->actingAs($user)
            ->post('/dating-profile/visibility', ['visibility_status' => 'hidden'])
            ->assertRedirect('/dating-profile/preview');

        $profile->refresh();
        $this->assertSame('hidden', $profile->visibility_status);

        $this->actingAs($user)
            ->post('/dating-profile/visibility', ['visibility_status' => 'visible'])
            ->assertRedirect('/dating-profile/preview');

        $profile->refresh();
        $this->assertSame('visible', $profile->visibility_status);
    }

    /**
     * @return array{User, DatingProfile}
     */
    private function createUserWithProfile(): array
    {
        $user = User::query()->create([
            'name' => 'Profile User',
            'email' => 'profile.visibility@example.com',
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $profile = DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Sanyi',
            'birthdate' => '1990-01-01',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => 47.4979,
            'approximate_longitude' => 19.0402,
            'relationship_intent' => 'long_term',
            'bio' => 'Original bio.',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => true,
            'completed_at' => '2026-07-19 12:00:00',
        ]);

        return [$user, $profile];
    }
}
