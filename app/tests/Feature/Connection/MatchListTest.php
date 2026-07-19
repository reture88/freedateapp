<?php

declare(strict_types=1);

namespace Tests\Feature\Connection;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MatchListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_active_matched_connections(): void
    {
        $viewer = $this->createProfile('matches.viewer@example.com', ['display_name' => 'Viewer']);
        $matched = $this->createProfile('matches.good@example.com', ['display_name' => 'Visible Match']);
        $pending = $this->createProfile('matches.pending@example.com', ['display_name' => 'Pending Match']);
        $unmatched = $this->createProfile('matches.unmatched@example.com', ['display_name' => 'Unmatched Person']);
        $blocked = $this->createProfile('matches.blocked@example.com', ['display_name' => 'Blocked Person']);
        $moderated = $this->createProfile('matches.moderated@example.com', [
            'display_name' => 'Moderated Person',
            'moderation_status' => 'pending',
        ]);

        $matchedConnection = Connection::createBetween($viewer, $matched, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => '2026-07-19 12:00:00',
        ]);
        Conversation::query()->create(['connection_id' => $matchedConnection->getKey()]);
        Connection::createBetween($viewer, $pending, ['status' => 'pending']);
        Connection::createBetween($viewer, $unmatched, ['status' => 'unmatched', 'unmatched_at' => '2026-07-19 12:10:00']);
        Connection::createBetween($viewer, $blocked, ['status' => 'blocked', 'blocked_at' => '2026-07-19 12:20:00']);
        Connection::createBetween($viewer, $moderated, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => '2026-07-19 12:30:00',
        ]);

        $this->actingAs($viewer->user)
            ->get('/matches')
            ->assertOk()
            ->assertSee('Visible Match')
            ->assertSee('Open chat')
            ->assertDontSee('Pending Match')
            ->assertDontSee('Unmatched Person')
            ->assertDontSee('Blocked Person')
            ->assertDontSee('Moderated Person');
    }

    public function test_empty_match_list_shows_empty_state(): void
    {
        $viewer = $this->createProfile('matches.empty@example.com', ['display_name' => 'Viewer']);

        $this->actingAs($viewer->user)
            ->get('/matches')
            ->assertOk()
            ->assertSee('No matches yet.');
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Match User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Match User',
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
