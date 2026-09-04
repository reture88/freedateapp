<?php

declare(strict_types=1);

namespace Tests\Feature\Assistant;

use App\Events\MessageSent;
use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class AiWingmanPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_matched_conversation_receives_one_assistant_message_after_waiting_period(): void
    {
        $this->travelTo('2026-07-19 12:00:00');
        [, , $conversation] = $this->createMatchedConversation([
            'first' => ['display_name' => 'Sanyi', 'ai_wingman_enabled' => true],
            'second' => ['display_name' => 'Mari', 'bio' => 'Harom macska es hetvegi turak.'],
            'matched_at' => '2026-07-19 05:00:00',
        ]);
        Event::fake([MessageSent::class]);

        $this->artisan('assistant:post-wingman-starters')->assertSuccessful();

        $message = Message::query()->whereBelongsTo($conversation)->firstOrFail();
        $this->assertNull($message->sender_user_id);
        $this->assertSame('assistant', $message->sender_type);
        $this->assertStringContainsString('Sanyi', $message->body);
        $this->assertStringContainsString('Mari', $message->body);
        Event::assertDispatched(MessageSent::class, static fn (MessageSent $event): bool => (int) $event->message->getKey() === (int) $message->getKey());

        $this->artisan('assistant:post-wingman-starters')->assertSuccessful();
        $this->assertSame(1, Message::query()->whereBelongsTo($conversation)->count());
    }

    public function test_conversations_with_user_messages_are_skipped(): void
    {
        $this->travelTo('2026-07-19 12:00:00');
        [$first, , $conversation] = $this->createMatchedConversation([
            'first' => ['ai_wingman_enabled' => true],
            'matched_at' => '2026-07-19 05:00:00',
        ]);
        Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => $first->user_id,
            'sender_type' => 'user',
            'body' => 'Szia!',
        ]);

        $this->artisan('assistant:post-wingman-starters')->assertSuccessful();

        $this->assertSame(1, Message::query()->whereBelongsTo($conversation)->count());
    }

    public function test_recent_matches_are_skipped_until_waiting_period_passes(): void
    {
        $this->travelTo('2026-07-19 12:00:00');
        [, , $conversation] = $this->createMatchedConversation([
            'first' => ['ai_wingman_enabled' => true],
            'matched_at' => '2026-07-19 11:00:00',
        ]);

        $this->artisan('assistant:post-wingman-starters')->assertSuccessful();

        $this->assertSame(0, Message::query()->whereBelongsTo($conversation)->count());
    }

    /**
     * @param array{first?: array<string, mixed>, second?: array<string, mixed>, matched_at?: string} $overrides
     *
     * @return array{DatingProfile, DatingProfile, Conversation}
     */
    private function createMatchedConversation(array $overrides = []): array
    {
        $first = $this->createProfile('assistant.first@example.com', $overrides['first'] ?? []);
        $second = $this->createProfile('assistant.second@example.com', $overrides['second'] ?? []);
        $connection = Connection::createBetween($first, $second, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => $overrides['matched_at'] ?? '2026-07-19 05:00:00',
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
            'name' => $overrides['display_name'] ?? 'Assistant User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Assistant User',
            'birthdate' => '1992-01-01',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'bio' => null,
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-07-19 12:00:00',
        ], $overrides));
    }
}
