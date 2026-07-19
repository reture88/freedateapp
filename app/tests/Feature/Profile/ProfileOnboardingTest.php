<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\DatingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProfileOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_complete_dating_profile_onboarding(): void
    {
        $user = User::query()->create([
            'name' => 'Onboarding User',
            'email' => 'onboarding@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/dating-profile/onboarding')
            ->assertOk()
            ->assertSee('Dating profile onboarding');

        $response = $this->actingAs($user)->post('/dating-profile/onboarding', [
            'display_name' => 'Rafi',
            'birthdate' => '1990-05-20',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => '47.497900',
            'approximate_longitude' => '19.040200',
            'relationship_intent' => 'long_term',
            'bio' => 'Coffee, walks, and dry humor.',
            'ai_wingman_enabled' => '1',
            'privacy_consent' => '1',
        ]);

        $response->assertRedirect('/dating-profile/preview');

        $profile = DatingProfile::query()->whereBelongsTo($user)->firstOrFail();

        $this->assertSame('Rafi', $profile->display_name);
        $this->assertSame('visible', $profile->visibility_status);
        $this->assertSame('pending', $profile->moderation_status);
        $this->assertTrue($profile->ai_wingman_enabled);
        $this->assertNotNull($profile->completed_at);

        $this->actingAs($user)
            ->get('/dating-profile/preview')
            ->assertOk()
            ->assertSee('Rafi')
            ->assertSee('Coffee, walks, and dry humor.');
    }

    public function test_incomplete_or_invalid_profile_data_is_rejected(): void
    {
        $user = User::query()->create([
            'name' => 'Invalid User',
            'email' => 'invalid@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from('/dating-profile/onboarding')
            ->post('/dating-profile/onboarding', [
                'display_name' => '',
                'birthdate' => '2012-01-01',
                'privacy_consent' => '0',
            ])
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors(['display_name', 'gender', 'orientation', 'privacy_consent']);

        $this->assertDatabaseCount('dating_profiles', 0);
    }
}
