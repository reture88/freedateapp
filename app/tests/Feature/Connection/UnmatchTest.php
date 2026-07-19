<?php

declare(strict_types=1);

namespace Tests\Feature\Connection;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UnmatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_either_matched_user_can_unmatch_connection(): void
    {
        [$first, $second, $connection, $conversation] = $this->createMatchedConnection();

        $this->actingAs($second->user)
            ->post(sprintf('/connections/%d/unmatch', $connection->getKey()))
            ->assertRedirect('/matches');

        $connection->refresh();
        $this->assertSame('unmatched', $connection->status);
        $this->assertNotNull($connection->unmatched_at);

        $this->actingAs($first->user)
            ->get('/matches')
            ->assertOk()
            ->assertDontSee($second->display_name);

        $this->actingAs($first->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertForbidden();
    }

    public function test_non_participant_cannot_unmatch_connection(): void
    {
        [, , $connection] = $this->createMatchedConnection();
        $outsider = $this->createProfile('unmatch.outsider@example.com');

        $this->actingAs($outsider->user)
            ->post(sprintf('/connections/%d/unmatch', $connection->getKey()))
            ->assertForbidden();

        $connection->refresh();
        $this->assertSame('matched', $connection->status);
    }

    /**
     * @return array{DatingProfile, DatingProfile, Connection, Conversation}
     */
    private function createMatchedConnection(): array
    {
        $first = $this->createProfile('unmatch.first@example.com', ['display_name' => 'Elso']);
        $second = $this->createProfile('unmatch.second@example.com', ['display_name' => 'Masodik']);
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
            'name' => $overrides['display_name'] ?? 'Unmatch User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Unmatch User',
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
}
