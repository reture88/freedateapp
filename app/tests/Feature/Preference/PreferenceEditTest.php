<?php

declare(strict_types=1);

namespace Tests\Feature\Preference;

use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PreferenceEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_dating_profile_is_redirected_to_onboarding(): void
    {
        $user = $this->createUser('preferences.no-profile@example.com');

        $this->actingAs($user)
            ->get('/dating-preferences/edit')
            ->assertRedirect('/dating-profile/onboarding');
    }

    public function test_defaults_exist_after_profile_completion_and_user_can_update_preferences(): void
    {
        $user = $this->createUser('preferences@example.com');

        $this->actingAs($user)->post('/dating-profile/onboarding', [
            'display_name' => 'Preference User',
            'birthdate' => '1992-03-12',
            'gender' => 'woman',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'privacy_consent' => '1',
        ])->assertRedirect('/dating-profile/preview');

        $profile = DatingProfile::query()->whereBelongsTo($user)->firstOrFail();
        $this->assertDatabaseHas('profile_preferences', [
            'dating_profile_id' => $profile->getKey(),
            'preferred_age_min' => 18,
            'preferred_age_max' => 80,
            'preferred_distance_km' => 50,
            'exclude_ai_wingman_enabled' => false,
        ]);

        $this->actingAs($user)
            ->get('/dating-preferences/edit')
            ->assertOk()
            ->assertSee('Dating preferences')
            ->assertSee('Preferred age');

        $response = $this->actingAs($user)->post('/dating-preferences', [
            'preferred_age_min' => '28',
            'preferred_age_max' => '42',
            'preferred_distance_km' => '35',
            'preferred_genders' => ['man', 'non_binary'],
            'preferred_orientations' => ['straight', 'bisexual'],
            'relationship_intents' => ['long_term', 'friendship'],
            'exclude_ai_wingman_enabled' => '1',
            'dealbreakers' => ['smoker'],
            'soft_preferences' => ['pets', 'weekend_trips'],
        ]);

        $response->assertRedirect('/dating-preferences/edit');

        $preference = ProfilePreference::query()->whereBelongsTo($profile)->firstOrFail();
        $this->assertSame(28, $preference->preferred_age_min);
        $this->assertSame(42, $preference->preferred_age_max);
        $this->assertSame(35, $preference->preferred_distance_km);
        $this->assertSame(['man', 'non_binary'], $preference->preferred_genders);
        $this->assertSame(['straight', 'bisexual'], $preference->preferred_orientations);
        $this->assertSame(['long_term', 'friendship'], $preference->relationship_intents);
        $this->assertTrue($preference->exclude_ai_wingman_enabled);
        $this->assertSame(['smoker'], $preference->dealbreakers);
        $this->assertSame(['pets', 'weekend_trips'], $preference->soft_preferences);
    }

    public function test_invalid_preference_ranges_are_rejected(): void
    {
        [$user, $profile] = $this->createUserWithProfile();

        $this->actingAs($user)
            ->from('/dating-preferences/edit')
            ->post('/dating-preferences', [
                'preferred_age_min' => '48',
                'preferred_age_max' => '30',
                'preferred_distance_km' => '0',
                'preferred_genders' => [],
                'preferred_orientations' => ['straight'],
                'relationship_intents' => ['long_term'],
            ])
            ->assertRedirect('/dating-preferences/edit')
            ->assertSessionHasErrors(['preferred_age_min', 'preferred_distance_km', 'preferred_genders']);

        $this->assertDatabaseMissing('profile_preferences', [
            'dating_profile_id' => $profile->getKey(),
            'preferred_age_min' => 48,
        ]);
    }

    /**
     * @return array{User, DatingProfile}
     */
    private function createUserWithProfile(): array
    {
        $user = $this->createUser('preference.profile@example.com');

        $profile = DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Preference Profile',
            'birthdate' => '1991-01-01',
            'gender' => 'man',
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

    private function createUser(string $email): User
    {
        return User::query()->create([
            'name' => 'Preference User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);
    }
}
