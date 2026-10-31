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
            'email_verified_at' => now(),
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
        $this->seed(\Database\Seeders\ProfileTagTaxonomySeeder::class);
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
        $coffee = \App\Models\ProfileTag::query()->where('slug', 'coffee')->firstOrFail();
        $running = \App\Models\ProfileTag::query()->where('slug', 'running')->firstOrFail();
        $cats = \App\Models\ProfileTag::query()->where('slug', 'cats')->firstOrFail();
        $viewer->tags()->sync([
            $coffee->getKey() => ['visibility' => 'public', 'sort_order' => 0],
            $cats->getKey() => ['visibility' => 'private_matching', 'sort_order' => 1],
        ]);
        $candidate->tags()->sync([
            $coffee->getKey() => ['visibility' => 'public', 'sort_order' => 0],
            $running->getKey() => ['visibility' => 'public', 'sort_order' => 1],
            $cats->getKey() => ['visibility' => 'private_matching', 'sort_order' => 2],
        ]);

        $this->actingAs($viewer->user)
            ->get('/discovery')
            ->assertOk()
            ->assertSee('Marika')
            ->assertSee('Budapest')
            ->assertSee('long_term')
            ->assertSee('Kave, koncertek, hetvegi kirandulasok.')
            ->assertSee('profiles/marika.jpg')
            ->assertSee('Shared interests')
            ->assertSee('Coffee')
            ->assertSee('More about Marika')
            ->assertSee('About Marika')
            ->assertSee('Interests')
            ->assertSee('Running')
            ->assertSee('Personality &amp; intent', false)
            ->assertDontSee('Cats')
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

    public function test_candidate_with_more_shared_tags_is_ranked_first(): void
    {
        $this->seed(\Database\Seeders\ProfileTagTaxonomySeeder::class);
        $viewer = $this->createProfile('viewer.ranking@example.com', ['display_name' => 'Viewer']);
        $this->createPreference($viewer);
        $fewMatches = $this->createProfile('candidate.low-ranking@example.com', [
            'display_name' => 'Few Shared Tags',
        ]);
        $manyMatches = $this->createProfile('candidate.high-ranking@example.com', [
            'display_name' => 'Many Shared Tags',
        ]);

        $coffee = \App\Models\ProfileTag::query()->where('slug', 'coffee')->firstOrFail();
        $hiking = \App\Models\ProfileTag::query()->where('slug', 'hiking')->firstOrFail();
        $cats = \App\Models\ProfileTag::query()->where('slug', 'cats')->firstOrFail();

        $viewer->tags()->sync([
            $coffee->getKey() => ['visibility' => 'public', 'sort_order' => 0],
            $hiking->getKey() => ['visibility' => 'private_matching', 'sort_order' => 1],
            $cats->getKey() => ['visibility' => 'private_matching', 'sort_order' => 2],
        ]);
        $fewMatches->tags()->sync([
            $coffee->getKey() => ['visibility' => 'public', 'sort_order' => 0],
        ]);
        $manyMatches->tags()->sync([
            $coffee->getKey() => ['visibility' => 'public', 'sort_order' => 0],
            $hiking->getKey() => ['visibility' => 'private_matching', 'sort_order' => 1],
            $cats->getKey() => ['visibility' => 'private_matching', 'sort_order' => 2],
        ]);

        $this->actingAs($viewer->user)
            ->get('/discovery')
            ->assertOk()
            ->assertSee('Many Shared Tags')
            ->assertDontSee('Few Shared Tags');
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Discovery User',
            'email' => $email,
            'email_verified_at' => now(),
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
