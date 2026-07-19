<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AccountLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_deactivate_account_and_request_deletion(): void
    {
        $profile = $this->createProfile('lifecycle@example.com');

        $this->actingAs($profile->user)
            ->post('/account/deactivate')
            ->assertRedirect('/');

        $profile->user->refresh();
        $profile->refresh();
        $this->assertFalse($profile->user->is_active);
        $this->assertSame('hidden', $profile->visibility_status);

        $profile->user->forceFill(['is_active' => true])->save();

        $this->actingAs($profile->user)
            ->post('/account/request-deletion')
            ->assertRedirect('/');

        $profile->user->refresh();
        $profile->refresh();
        $this->assertNotNull($profile->user->deletion_requested_at);
        $this->assertSame('hidden', $profile->visibility_status);
    }

    public function test_inactive_or_deletion_requested_users_are_excluded_from_discovery_and_chat(): void
    {
        $viewer = $this->createProfile('lifecycle.viewer@example.com');
        $inactive = $this->createProfile('lifecycle.inactive@example.com', ['display_name' => 'Inactive Candidate']);
        $inactive->user->forceFill(['is_active' => false])->save();
        $deleting = $this->createProfile('lifecycle.deleting@example.com', ['display_name' => 'Deleting Candidate']);
        $deleting->user->forceFill(['deletion_requested_at' => '2026-07-19 12:00:00'])->save();
        $eligible = $this->createProfile('lifecycle.eligible@example.com', ['display_name' => 'Eligible Candidate']);
        $this->createPreference($viewer);

        $connection = Connection::createBetween($viewer, $inactive, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => '2026-07-19 12:00:00',
        ]);
        $conversation = Conversation::query()->create(['connection_id' => $connection->getKey()]);

        $this->actingAs($viewer->user)
            ->get('/discovery')
            ->assertOk()
            ->assertSee($eligible->display_name)
            ->assertDontSee($inactive->display_name)
            ->assertDontSee($deleting->display_name);

        $this->actingAs($viewer->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertForbidden();
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Lifecycle User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Lifecycle User',
            'birthdate' => '1992-01-01',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
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
