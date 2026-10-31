<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Models\User;
use App\User\Enum\AccountStatus;
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
        $this->assertSame(AccountStatus::DEACTIVATED, $profile->user->accountStatus());
        $this->assertFalse($profile->user->is_active);
        $this->assertSame('hidden', $profile->visibility_status);
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $profile->user->getKey(),
            'action' => 'deactivate_account',
            'target_type' => 'user',
            'target_id' => $profile->user->getKey(),
        ]);

        $profile->user->forceFill([
            'account_status' => AccountStatus::ACTIVE,
            'is_active' => true,
        ])->save();

        $this->actingAs($profile->user)
            ->post('/account/request-deletion')
            ->assertRedirect('/');

        $profile->user->refresh();
        $profile->refresh();
        $this->assertSame(AccountStatus::DELETION_REQUESTED, $profile->user->accountStatus());
        $this->assertFalse($profile->user->is_active);
        $this->assertNotNull($profile->user->deletion_requested_at);
        $this->assertSame('hidden', $profile->visibility_status);
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $profile->user->getKey(),
            'action' => 'request_account_deletion',
            'target_type' => 'user',
            'target_id' => $profile->user->getKey(),
        ]);
    }

    public function test_non_active_account_statuses_are_excluded_from_discovery_and_chat(): void
    {
        $viewer = $this->createProfile('lifecycle.viewer@example.com');
        $suspended = $this->createProfile('lifecycle.suspended@example.com', ['display_name' => 'Suspended Candidate']);
        $suspended->user->forceFill(['account_status' => AccountStatus::SUSPENDED, 'is_active' => false])->save();
        $banned = $this->createProfile('lifecycle.banned@example.com', ['display_name' => 'Banned Candidate']);
        $banned->user->forceFill(['account_status' => AccountStatus::BANNED, 'is_active' => false])->save();
        $deactivated = $this->createProfile('lifecycle.deactivated@example.com', ['display_name' => 'Deactivated Candidate']);
        $deactivated->user->forceFill(['account_status' => AccountStatus::DEACTIVATED, 'is_active' => false])->save();
        $deleting = $this->createProfile('lifecycle.deleting@example.com', ['display_name' => 'Deleting Candidate']);
        $deleting->user->forceFill([
            'account_status' => AccountStatus::DELETION_REQUESTED,
            'is_active' => false,
            'deletion_requested_at' => '2026-07-19 12:00:00',
        ])->save();
        $eligible = $this->createProfile('lifecycle.eligible@example.com', ['display_name' => 'Eligible Candidate']);
        $this->createPreference($viewer);

        $connection = Connection::createBetween($viewer, $suspended, [
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
            ->assertDontSee($suspended->display_name)
            ->assertDontSee($banned->display_name)
            ->assertDontSee($deactivated->display_name)
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
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
            'account_status' => AccountStatus::ACTIVE,
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
