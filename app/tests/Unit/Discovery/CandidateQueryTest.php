<?php

declare(strict_types=1);

namespace Tests\Unit\Discovery;

use App\Discovery\DomainService\FindNextCandidateService;
use App\Models\Block;
use App\Models\DatingProfile;
use App\Models\DiscoveryDecision;
use App\Models\ProfilePreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CandidateQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_query_returns_next_profile_matching_hard_filters(): void
    {
        $viewer = $this->createProfile('viewer@example.com', [
            'birthdate' => '1990-01-01',
            'gender' => 'woman',
            'orientation' => 'straight',
            'relationship_intent' => 'long_term',
            'approximate_latitude' => 47.4979,
            'approximate_longitude' => 19.0402,
        ]);
        $this->createPreference($viewer, [
            'preferred_age_min' => 28,
            'preferred_age_max' => 42,
            'preferred_distance_km' => 40,
            'preferred_genders' => ['man'],
            'preferred_orientations' => ['straight'],
            'relationship_intents' => ['long_term'],
            'exclude_ai_wingman_enabled' => true,
        ]);

        $eligible = $this->createProfile('eligible@example.com', [
            'display_name' => 'Eligible Candidate',
            'birthdate' => '1988-04-10',
            'gender' => 'man',
            'orientation' => 'straight',
            'relationship_intent' => 'long_term',
            'approximate_latitude' => 47.5000,
            'approximate_longitude' => 19.0500,
        ]);

        $this->createProfile('self-hidden@example.com', ['visibility_status' => 'hidden']);
        $this->createProfile('moderated@example.com', ['moderation_status' => 'pending']);
        $this->createProfile('too-young@example.com', ['birthdate' => '2004-01-01']);
        $this->createProfile('wrong-gender@example.com', ['gender' => 'woman']);
        $this->createProfile('wrong-orientation@example.com', ['orientation' => 'gay']);
        $this->createProfile('wrong-intent@example.com', ['relationship_intent' => 'friendship']);
        $this->createProfile('too-far@example.com', [
            'approximate_latitude' => 46.2530,
            'approximate_longitude' => 20.1414,
        ]);
        $this->createProfile('ai-enabled@example.com', ['ai_wingman_enabled' => true]);
        $decided = $this->createProfile('decided@example.com');
        $blocked = $this->createProfile('blocked@example.com');

        DiscoveryDecision::query()->create([
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $decided->getKey(),
            'decision' => 'rejected',
            'decided_at' => '2026-07-19 12:00:00',
        ]);
        Block::query()->create([
            'blocker_user_id' => $viewer->user_id,
            'blocked_user_id' => $blocked->user_id,
            'reason_category' => 'manual',
        ]);

        $candidate = (new FindNextCandidateService())->handle($viewer);

        $this->assertNotNull($candidate);
        $this->assertSame($eligible->getKey(), $candidate->getKey());
    }

    public function test_candidate_query_returns_null_when_no_candidate_exists(): void
    {
        $viewer = $this->createProfile('empty@example.com');
        $this->createPreference($viewer);

        $this->assertNull((new FindNextCandidateService())->handle($viewer));
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
            'birthdate' => '1988-01-01',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => 47.4979,
            'approximate_longitude' => 19.0402,
            'relationship_intent' => 'long_term',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-07-19 12:00:00',
        ], $overrides));
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createPreference(DatingProfile $profile, array $overrides = []): ProfilePreference
    {
        return ProfilePreference::query()->create(array_merge([
            'dating_profile_id' => $profile->getKey(),
            'preferred_age_min' => 18,
            'preferred_age_max' => 80,
            'preferred_distance_km' => 100,
            'preferred_genders' => ['woman', 'man', 'non_binary', 'other'],
            'preferred_orientations' => ['straight', 'gay', 'lesbian', 'bisexual', 'pansexual', 'queer', 'other'],
            'relationship_intents' => ['long_term', 'short_term', 'friendship', 'unsure'],
            'exclude_ai_wingman_enabled' => false,
            'dealbreakers' => [],
            'soft_preferences' => [],
        ], $overrides));
    }
}
