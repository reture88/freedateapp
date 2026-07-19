<?php

declare(strict_types=1);

namespace Tests\Feature\Discovery;

use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Models\ProfilePhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DiscoveryCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_dating_profile_is_redirected_to_onboarding(): void
    {
        $user = User::query()->create([
            'name' => 'Discovery User',
            'email' => 'viewer.no-profile@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/discovery')
            ->assertRedirect('/dating-profile/onboarding');
    }

    public function test_user_sees_one_candidate_card(): void
    {
        $viewer = $this->createProfile('viewer.card@example.com', ['display_name' => 'Viewer']);
        $this->createPreference($viewer);
        $candidate = $this->createProfile('candidate.card@example.com', [
            'display_name' => 'Marika',
            'birthdate' => '1994-07-10',
            'city' => 'Budapest',
            'relationship_intent' => 'long_term',
            'bio' => 'Kave, koncertek, hetvegi kirandulasok.',
        ]);
        ProfilePhoto::query()->create([
            'dating_profile_id' => $candidate->getKey(),
            'path' => 'profiles/marika.jpg',
            'sort_order' => 0,
            'is_primary' => true,
            'moderation_status' => 'approved',
        ]);

        $this->actingAs($viewer->user)
            ->get('/discovery')
            ->assertOk()
            ->assertSee('Marika')
            ->assertSee('Budapest')
            ->assertSee('long_term')
            ->assertSee('Kave, koncertek, hetvegi kirandulasok.')
            ->assertSee('profiles/marika.jpg')
            ->assertSee('Accept')
            ->assertSee('Reject');
    }

    public function test_empty_state_appears_when_no_candidates_exist(): void
    {
        $viewer = $this->createProfile('viewer.empty@example.com', ['display_name' => 'Viewer']);
        $this->createPreference($viewer);

        $this->actingAs($viewer->user)
            ->get('/discovery')
            ->assertOk()
            ->assertSee('No profiles available right now.');
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Discovery User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Discovery Candidate',
            'birthdate' => '1992-01-01',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => 47.4979,
            'approximate_longitude' => 19.0402,
            'relationship_intent' => 'long_term',
            'bio' => 'Default bio.',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-07-19 12:00:00',
        ], $overrides));
    }

    private function createPreference(DatingProfile $profile): ProfilePreference
    {
        return ProfilePreference::query()->create([
            'dating_profile_id' => $profile->getKey(),
            'preferred_age_min' => 18,
            'preferred_age_max' => 80,
            'preferred_distance_km' => 100,
            'preferred_genders' => ['man'],
            'preferred_orientations' => ['straight'],
            'relationship_intents' => ['long_term'],
            'exclude_ai_wingman_enabled' => false,
            'dealbreakers' => [],
            'soft_preferences' => [],
        ]);
    }
}
