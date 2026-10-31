<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Livewire\Chat\MessageList;
use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\Message;
use App\Models\ProfilePhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MessageListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_dating_profile_is_redirected_to_onboarding(): void
    {
        $user = User::query()->create([
            'name' => 'Messages User',
            'email' => 'messages.no-profile@example.com',
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/messages')
            ->assertRedirect('/dating-profile/onboarding');
    }

    public function test_messages_index_uses_real_matched_conversation_data(): void
    {
        $viewer = $this->createProfile('messages.viewer@example.com', ['display_name' => 'Viewer']);
        $unreadMatch = $this->createProfile('messages.unread@example.com', [
            'display_name' => 'Unread Match',
            'birthdate' => '1994-05-10',
        ]);
        $quietMatch = $this->createProfile('messages.quiet@example.com', [
            'display_name' => 'Quiet Match',
            'birthdate' => '1991-03-04',
        ]);
        $pending = $this->createProfile('messages.pending@example.com', ['display_name' => 'Pending Person']);

        $unreadConversation = $this->conversation($viewer, $unreadMatch, '2026-07-19 12:00:00');
        $quietConversation = $this->conversation($viewer, $quietMatch, '2026-07-16 12:00:00');
        Connection::createBetween($viewer, $pending, ['status' => 'pending']);

        ProfilePhoto::query()->create([
            'dating_profile_id' => $unreadMatch->getKey(),
            'path' => 'https://i.pravatar.cc/600?img=12',
            'sort_order' => 0,
            'is_primary' => true,
            'moderation_status' => 'approved',
        ]);

        Message::query()->create([
            'conversation_id' => $unreadConversation->getKey(),
            'sender_user_id' => $unreadMatch->user_id,
            'sender_type' => 'user',
            'body' => 'Coffee this week sounds perfect.',
            'read_at' => null,
            'created_at' => '2026-07-20 09:00:00',
            'updated_at' => '2026-07-20 09:00:00',
        ]);
        Message::query()->create([
            'conversation_id' => $quietConversation->getKey(),
            'sender_user_id' => $viewer->user_id,
            'sender_type' => 'user',
            'body' => 'I liked that museum too.',
            'read_at' => '2026-07-20 10:00:00',
            'created_at' => '2026-07-20 08:00:00',
            'updated_at' => '2026-07-20 08:00:00',
        ]);

        $this->actingAs($viewer->user)
            ->get('/messages')
            ->assertOk()
            ->assertSeeLivewire(MessageList::class)
            ->assertSee('Messages')
            ->assertSee('Conversations with your matches')
            ->assertSee('Unread Match, 32')
            ->assertSee('Coffee this week sounds perfect.')
            ->assertSee('Quiet Match, 35')
            ->assertSee('I liked that museum too.')
            ->assertSee(sprintf('/conversations/%d', $unreadConversation->getKey()))
            ->assertDontSee('Pending Person');
    }

    private function conversation(DatingProfile $viewer, DatingProfile $other, string $matchedAt): Conversation
    {
        $connection = Connection::createBetween($viewer, $other, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => $matchedAt,
        ]);

        return Conversation::query()->create(['connection_id' => $connection->getKey()]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Message User',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Message User',
            'birthdate' => '1992-01-01',
            'gender' => 'woman',
            'orientation' => 'bisexual',
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
