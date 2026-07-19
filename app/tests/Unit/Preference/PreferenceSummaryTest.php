<?php

declare(strict_types=1);

namespace Tests\Unit\Preference;

use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Models\User;
use App\Preference\DomainService\BuildPreferenceSummaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PreferenceSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_exposes_normalized_hard_filters_for_discovery(): void
    {
        $user = User::query()->create([
            'name' => 'Summary User',
            'email' => 'summary@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $profile = DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Summary Profile',
            'birthdate' => '1991-01-01',
            'gender' => 'woman',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => true,
            'completed_at' => '2026-07-19 12:00:00',
        ]);

        ProfilePreference::query()->create([
            'dating_profile_id' => $profile->getKey(),
            'preferred_age_min' => 26,
            'preferred_age_max' => 39,
            'preferred_distance_km' => 75,
            'preferred_genders' => ['man'],
            'preferred_orientations' => ['straight', 'bisexual'],
            'relationship_intents' => ['long_term'],
            'exclude_ai_wingman_enabled' => true,
            'dealbreakers' => ['smoker'],
            'soft_preferences' => ['pets'],
        ]);

        $summary = (new BuildPreferenceSummaryService())->handle($profile);

        $this->assertSame([
            'age_min' => 26,
            'age_max' => 39,
            'distance_km' => 75,
            'genders' => ['man'],
            'orientations' => ['straight', 'bisexual'],
            'relationship_intents' => ['long_term'],
            'exclude_ai_wingman_enabled' => true,
        ], $summary->hardFilters);

        $this->assertSame('Ages 26-39, within 75 km, looking for long-term relationship', $summary->displayText);
    }
}
