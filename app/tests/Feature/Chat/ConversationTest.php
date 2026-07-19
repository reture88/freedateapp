<?php

declare(strict_types=1);

namespace Tests\Feature\Chat;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\Message;
use App\Models\ProfilePhoto;
use App\Models\ProfileTag;
use App\Models\User;
use Database\Seeders\ProfileTagTaxonomySeeder;
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

    public function test_conversation_shows_messages_oldest_to_newest_and_links_other_profile(): void
    {
        [$first, $second, $conversation] = $this->createMatchedConversation();
        ProfilePhoto::query()->create([
            'dating_profile_id' => $second->getKey(),
            'path' => 'https://i.pravatar.cc/600?img=7',
            'sort_order' => 0,
            'is_primary' => true,
            'moderation_status' => 'approved',
        ]);

        Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => $first->user_id,
            'sender_type' => 'user',
            'body' => 'First inserted message',
            'created_at' => '2026-07-19 17:00:00',
            'updated_at' => '2026-07-19 17:00:00',
        ]);
        Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => $second->user_id,
            'sender_type' => 'user',
            'body' => 'Second inserted message',
            'created_at' => '2026-07-19 15:00:00',
            'updated_at' => '2026-07-19 15:00:00',
        ]);

        $this->actingAs($first->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertOk()
            ->assertSeeInOrder(['First inserted message', 'Second inserted message'])
            ->assertSee('https://i.pravatar.cc/600?img=7')
            ->assertSee(sprintf('/profiles/%d', $second->getKey()));
    }

    public function test_matched_user_can_view_other_profile_from_chat(): void
    {
        [$first, $second] = $this->createMatchedConversation();
        $this->seed(ProfileTagTaxonomySeeder::class);
        $coffee = ProfileTag::query()->where('slug', 'coffee')->firstOrFail();
        $hiking = ProfileTag::query()->where('slug', 'hiking')->firstOrFail();
        $cats = ProfileTag::query()->where('slug', 'cats')->firstOrFail();

        $second->tags()->sync([
            $coffee->getKey() => ['visibility' => 'public', 'sort_order' => 0],
            $hiking->getKey() => ['visibility' => 'matches_only', 'sort_order' => 1],
            $cats->getKey() => ['visibility' => 'private_matching', 'sort_order' => 2],
        ]);

        $this->actingAs($first->user)
            ->get(sprintf('/profiles/%d', $second->getKey()))
            ->assertOk()
            ->assertSee($second->display_name)
            ->assertSee($second->bio)
            ->assertSee('Coffee')
            ->assertSee('Hiking')
            ->assertDontSee('Cats');
    }

    public function test_unmatched_user_cannot_view_other_profile(): void
    {
        [, $second] = $this->createMatchedConversation();
        $outsider = $this->createProfile('profile.outsider@example.com');

        $this->actingAs($outsider->user)
            ->get(sprintf('/profiles/%d', $second->getKey()))
            ->assertForbidden();
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
