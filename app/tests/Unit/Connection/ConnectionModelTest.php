<?php

declare(strict_types=1);

namespace Tests\Unit\Connection;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\DiscoveryDecision;
use App\Models\Message;
use App\Models\ProfilePhoto;
use App\Models\ProfilePreference;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ConnectionModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_owns_photos_preferences_and_discovery_decisions(): void
    {
        [$viewer, $target] = $this->createProfilePair();

        $photo = ProfilePhoto::query()->create([
            'dating_profile_id' => $viewer->getKey(),
            'path' => 'profile-photos/viewer.jpg',
            'sort_order' => 1,
            'is_primary' => true,
            'moderation_status' => 'approved',
        ]);

        $preference = ProfilePreference::query()->create([
            'dating_profile_id' => $viewer->getKey(),
            'preferred_age_min' => 25,
            'preferred_age_max' => 40,
            'preferred_distance_km' => 50,
            'preferred_genders' => ['woman'],
            'preferred_orientations' => ['straight'],
            'relationship_intents' => ['long_term'],
            'exclude_ai_wingman_enabled' => true,
            'dealbreakers' => ['smoking'],
            'soft_preferences' => ['pets' => 'likes_cats'],
        ]);

        $decision = DiscoveryDecision::query()->create([
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $target->getKey(),
            'decision' => 'accepted',
            'decided_at' => '2026-07-19 13:00:00',
        ]);

        $this->assertTrue($viewer->photos()->first()->is($photo));
        $this->assertTrue($viewer->preference()->is($preference));
        $this->assertTrue($viewer->discoveryDecisionsMade()->first()->is($decision));
        $this->assertTrue($target->discoveryDecisionsReceived()->first()->is($decision));
        $this->assertSame(['woman'], $preference->preferred_genders);
        $this->assertTrue($photo->is_primary);
    }

    public function test_discovery_decision_is_unique_per_viewer_and_target(): void
    {
        [$viewer, $target] = $this->createProfilePair();

        DiscoveryDecision::query()->create([
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $target->getKey(),
            'decision' => 'rejected',
            'decided_at' => '2026-07-19 13:00:00',
        ]);

        $this->expectException(QueryException::class);

        DiscoveryDecision::query()->create([
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $target->getKey(),
            'decision' => 'accepted',
            'decided_at' => '2026-07-19 13:05:00',
        ]);
    }

    public function test_connection_uses_deterministic_profile_order_and_has_conversation_messages(): void
    {
        [$firstProfile, $secondProfile] = $this->createProfilePair();

        $connection = Connection::createBetween($secondProfile, $firstProfile, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'pending',
            'status' => 'pending',
        ]);

        $conversation = Conversation::query()->create([
            'connection_id' => $connection->getKey(),
        ]);

        $message = Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => $firstProfile->user_id,
            'sender_type' => 'user',
            'body' => 'Szia!',
            'read_at' => null,
        ]);

        $this->assertSame($firstProfile->getKey(), $connection->profile_one_id);
        $this->assertSame($secondProfile->getKey(), $connection->profile_two_id);
        $this->assertTrue($connection->profileOne()->is($firstProfile));
        $this->assertTrue($connection->profileTwo()->is($secondProfile));
        $this->assertTrue($connection->conversation()->is($conversation));
        $this->assertTrue($conversation->messages()->first()->is($message));
        $this->assertTrue($message->conversation()->is($conversation));
    }

    /**
     * @return array{DatingProfile, DatingProfile}
     */
    private function createProfilePair(): array
    {
        $firstUser = User::query()->create([
            'name' => 'First User',
            'email' => 'first.user@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $secondUser = User::query()->create([
            'name' => 'Second User',
            'email' => 'second.user@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return [
            DatingProfile::query()->create([
                'user_id' => $firstUser->getKey(),
                'display_name' => 'Sanyi',
                'birthdate' => '1990-01-01',
                'gender' => 'man',
                'orientation' => 'straight',
                'city' => 'Budapest',
                'country_code' => 'HU',
                'approximate_latitude' => 47.4979,
                'approximate_longitude' => 19.0402,
                'relationship_intent' => 'long_term',
                'bio' => 'Likes cats.',
                'visibility_status' => 'visible',
                'moderation_status' => 'approved',
                'ai_wingman_enabled' => false,
                'completed_at' => '2026-07-19 12:00:00',
            ]),
            DatingProfile::query()->create([
                'user_id' => $secondUser->getKey(),
                'display_name' => 'Mari',
                'birthdate' => '1992-02-02',
                'gender' => 'woman',
                'orientation' => 'straight',
                'city' => 'Budapest',
                'country_code' => 'HU',
                'approximate_latitude' => 47.5010,
                'approximate_longitude' => 19.0450,
                'relationship_intent' => 'long_term',
                'bio' => 'Has three cats.',
                'visibility_status' => 'visible',
                'moderation_status' => 'approved',
                'ai_wingman_enabled' => true,
                'completed_at' => '2026-07-19 12:00:00',
            ]),
        ];
    }
}
