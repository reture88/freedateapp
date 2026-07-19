<?php

declare(strict_types=1);

namespace Tests\Feature\Safety;

use App\Models\Block;
use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BlockFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocking_user_blocks_existing_connection_and_chat_access(): void
    {
        [$blocker, $blocked, $connection, $conversation] = $this->createMatchedConnection();

        $this->actingAs($blocker->user)
            ->post('/blocks', [
                'blocked_user_id' => $blocked->user_id,
                'reason_category' => 'safety',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('blocks', [
            'blocker_user_id' => $blocker->user_id,
            'blocked_user_id' => $blocked->user_id,
            'reason_category' => 'safety',
        ]);
        $connection->refresh();
        $this->assertSame('blocked', $connection->status);
        $this->assertNotNull($connection->blocked_at);

        $this->actingAs($blocked->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertForbidden();
    }

    public function test_blocked_users_do_not_appear_in_discovery(): void
    {
        $viewer = $this->createProfile('block.viewer@example.com');
        $blocked = $this->createProfile('block.blocked@example.com', ['display_name' => 'Blocked Candidate']);
        $eligible = $this->createProfile('block.eligible@example.com', ['display_name' => 'Eligible Candidate']);
        $this->createPreference($viewer);

        Block::query()->create([
            'blocker_user_id' => $blocked->user_id,
            'blocked_user_id' => $viewer->user_id,
            'reason_category' => 'safety',
        ]);

        $this->actingAs($viewer->user)
            ->get('/discovery')
            ->assertOk()
            ->assertSee($eligible->display_name)
            ->assertDontSee($blocked->display_name);
    }

    /**
     * @return array{DatingProfile, DatingProfile, Connection, Conversation}
     */
    private function createMatchedConnection(): array
    {
        $first = $this->createProfile('block.first@example.com');
        $second = $this->createProfile('block.second@example.com');
        $connection = Connection::createBetween($first, $second, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => '2026-07-19 12:00:00',
        ]);
        $conversation = Conversation::query()->create(['connection_id' => $connection->getKey()]);

        return [$first, $second, $connection, $conversation];
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Block User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Block User',
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
