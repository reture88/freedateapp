<?php

declare(strict_types=1);

namespace Tests\Feature\Connection;

use App\Livewire\Connection\MatchList;
use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\Message;
use App\Models\ProfilePhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class MatchListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_dating_profile_is_redirected_to_onboarding(): void
    {
        $user = User::query()->create([
            'name' => 'Match User',
            'email' => 'matches.no-profile@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/matches')
            ->assertRedirect('/dating-profile/onboarding');
    }

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
        ProfilePhoto::query()->create([
            'dating_profile_id' => $matched->getKey(),
            'path' => 'profile-photos/visible-match.jpg',
            'sort_order' => 0,
            'is_primary' => true,
            'moderation_status' => 'approved',
        ]);
        $conversation = Conversation::query()->create(['connection_id' => $matchedConnection->getKey()]);
        Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => $matched->user_id,
            'sender_type' => 'user',
            'body' => 'Hello',
            'read_at' => null,
        ]);
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
            ->assertSee('Visible Match, 34')
            ->assertSee('Unread message')
            ->assertSee('Open chat')
            ->assertSeeLivewire(MatchList::class)
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

    public function test_livewire_match_list_prepends_broadcasted_match_once(): void
    {
        $viewer = $this->createProfile('matches.livewire.viewer@example.com', ['display_name' => 'Viewer']);
        $other = $this->createProfile('matches.livewire.other@example.com', ['display_name' => 'Realtime Match']);

        Livewire::actingAs($viewer->user)
            ->test(MatchList::class, ['matches' => []])
            ->call('prependBroadcastedMatch', [
                'connection_id' => 123,
                'conversation_id' => 456,
                'matched_at' => '2026-07-19 12:00:00',
                'profiles' => [
                    ['id' => $viewer->getKey(), 'display_name' => 'Viewer', 'city' => 'Budapest'],
                    ['id' => $other->getKey(), 'display_name' => 'Realtime Match', 'city' => 'Szeged'],
                ],
            ])
            ->call('prependBroadcastedMatch', [
                'connection_id' => 123,
                'conversation_id' => 456,
                'matched_at' => '2026-07-19 12:00:00',
                'profiles' => [
                    ['id' => $viewer->getKey(), 'display_name' => 'Viewer', 'city' => 'Budapest'],
                    ['id' => $other->getKey(), 'display_name' => 'Realtime Match', 'city' => 'Szeged'],
                ],
            ])
            ->assertSee('Realtime Match')
            ->assertSet('matches', [
                [
                    'connection_id' => 123,
                    'conversation_id' => 456,
                    'display_name' => 'Realtime Match',
                    'age' => null,
                    'city' => 'Szeged',
                    'primary_photo_url' => null,
                    'matched_at' => '2026-07-19 12:00:00',
                    'unread_count' => 0,
                    'latest_message_preview' => null,
                    'latest_message_at' => null,
                    'state' => 'default',
                    'status_label' => 'New match',
                ],
            ]);
    }

    public function test_private_user_broadcast_channel_is_limited_to_own_user(): void
    {
        $viewer = $this->createProfile('matches.broadcast.viewer@example.com');
        $other = $this->createProfile('matches.broadcast.other@example.com');
        $payload = [
            'socket_id' => '1234.5678',
            'channel_name' => sprintf('private-users.%d', $viewer->user_id),
        ];

        $this->actingAs($viewer->user)
            ->post('/broadcasting/auth', $payload)
            ->assertOk();

        $this->actingAs($other->user)
            ->post('/broadcasting/auth', $payload)
            ->assertForbidden();
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
