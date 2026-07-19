<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_matched_user_can_open_empty_conversation_and_send_message(): void
    {
        [$first, $second, $conversation] = $this->createMatchedConversation();

        $this->actingAs($first->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertOk()
            ->assertSee($second->display_name)
            ->assertSee('No messages yet.');

        $this->actingAs($first->user)
            ->post(sprintf('/conversations/%d/messages', $conversation->getKey()), [
                'body' => 'Szia, van kedved kavezni?',
            ])
            ->assertRedirect(sprintf('/conversations/%d', $conversation->getKey()));

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => $first->user_id,
            'sender_type' => 'user',
            'body' => 'Szia, van kedved kavezni?',
        ]);

        $this->actingAs($second->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertOk()
            ->assertSee('Szia, van kedved kavezni?');
    }

    public function test_unmatched_user_cannot_open_conversation(): void
    {
        [, , $conversation] = $this->createMatchedConversation();
        $outsider = $this->createProfile('outsider@example.com');

        $this->actingAs($outsider->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertForbidden();
    }

    public function test_message_body_is_required_and_limited(): void
    {
        [$first, , $conversation] = $this->createMatchedConversation();

        $this->actingAs($first->user)
            ->from(sprintf('/conversations/%d', $conversation->getKey()))
            ->post(sprintf('/conversations/%d/messages', $conversation->getKey()), ['body' => ''])
            ->assertRedirect(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertSessionHasErrors(['body']);

        $this->actingAs($first->user)
            ->from(sprintf('/conversations/%d', $conversation->getKey()))
            ->post(sprintf('/conversations/%d/messages', $conversation->getKey()), ['body' => str_repeat('a', 2001)])
            ->assertRedirect(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertSessionHasErrors(['body']);

        $this->assertSame(0, Message::query()->count());
    }

    /**
     * @return array{DatingProfile, DatingProfile, Conversation}
     */
    private function createMatchedConversation(): array
    {
        $first = $this->createProfile('chat.first@example.com', ['display_name' => 'Elso']);
        $second = $this->createProfile('chat.second@example.com', ['display_name' => 'Masodik']);
        $connection = Connection::createBetween($first, $second, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => '2026-07-19 12:00:00',
        ]);
        $conversation = Conversation::query()->create(['connection_id' => $connection->getKey()]);

        return [$first, $second, $conversation];
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Chat User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Chat User',
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
