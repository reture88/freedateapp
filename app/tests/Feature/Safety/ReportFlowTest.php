<?php

declare(strict_types=1);

namespace Tests\Feature\Safety;

use App\Events\ReportSubmitted;
use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class ReportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_report_profile_message_and_assistant_message(): void
    {
        [$reporter, $target, $conversation] = $this->createMatchedConversation();
        $userMessage = Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => $target->user_id,
            'sender_type' => 'user',
            'body' => 'Problematic text.',
        ]);
        $assistantMessage = Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => null,
            'sender_type' => 'assistant',
            'body' => 'Assistant text.',
        ]);
        Event::fake([ReportSubmitted::class]);

        $this->actingAs($reporter->user)
            ->post('/reports', [
                'target_type' => 'profile',
                'target_id' => $target->getKey(),
                'category' => 'fake_profile',
                'note' => 'This looks suspicious.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->actingAs($reporter->user)
            ->post('/reports', [
                'target_type' => 'message',
                'target_id' => $userMessage->getKey(),
                'category' => 'harassment',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->actingAs($reporter->user)
            ->post('/reports', [
                'target_type' => 'assistant_message',
                'target_id' => $assistantMessage->getKey(),
                'category' => 'unsafe_ai',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reports', [
            'reporter_user_id' => $reporter->user_id,
            'target_type' => 'profile',
            'target_id' => $target->getKey(),
            'category' => 'fake_profile',
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('reports', [
            'reporter_user_id' => $reporter->user_id,
            'target_type' => 'message',
            'target_id' => $userMessage->getKey(),
            'category' => 'harassment',
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('reports', [
            'reporter_user_id' => $reporter->user_id,
            'target_type' => 'assistant_message',
            'target_id' => $assistantMessage->getKey(),
            'category' => 'unsafe_ai',
            'status' => 'open',
        ]);
        Event::assertDispatchedTimes(ReportSubmitted::class, 3);
    }

    public function test_invalid_report_payload_is_rejected(): void
    {
        $reporter = $this->createProfile('report.invalid@example.com');

        $this->actingAs($reporter->user)
            ->from('/discovery')
            ->post('/reports', [
                'target_type' => 'unknown',
                'target_id' => '',
                'category' => '',
                'note' => str_repeat('a', 2001),
            ])
            ->assertRedirect('/discovery')
            ->assertSessionHasErrors(['target_type', 'target_id', 'category', 'note']);
    }

    public function test_admin_report_broadcast_channel_is_limited_to_admins(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin User',
            'email' => 'report.admin@example.com',
            'password' => 'secret123',
            'role' => 'admin',
            'is_admin' => true,
            'is_active' => true,
        ]);
        $customer = User::query()->create([
            'name' => 'Customer User',
            'email' => 'report.customer@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);
        $payload = [
            'socket_id' => '1234.5678',
            'channel_name' => 'private-admin.reports',
        ];

        $this->actingAs($admin)
            ->post('/broadcasting/auth', $payload)
            ->assertOk();

        $this->actingAs($customer)
            ->post('/broadcasting/auth', $payload)
            ->assertForbidden();
    }

    /**
     * @return array{DatingProfile, DatingProfile, Conversation}
     */
    private function createMatchedConversation(): array
    {
        $first = $this->createProfile('report.first@example.com');
        $second = $this->createProfile('report.second@example.com');
        $connection = Connection::createBetween($first, $second, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => '2026-07-19 12:00:00',
        ]);
        $conversation = Conversation::query()->create(['connection_id' => $connection->getKey()]);

        return [$first, $second, $conversation];
    }

    private function createProfile(string $email): DatingProfile
    {
        $user = User::query()->create([
            'name' => 'Report User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Report User',
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
        ]);
    }
}
