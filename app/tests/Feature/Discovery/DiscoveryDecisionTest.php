<?php

declare(strict_types=1);

namespace Tests\Feature\Discovery;

use App\Events\MatchCreated;
use App\Livewire\Discovery\DiscoveryCard;
use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\DiscoveryDecision;
use App\Models\ProfilePreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

final class DiscoveryDecisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_reject_records_decision_and_advances_queue(): void
    {
        $viewer = $this->createProfile('reject.viewer@example.com');
        $first = $this->createProfile('reject.first@example.com', ['display_name' => 'First Candidate']);
        $second = $this->createProfile('reject.second@example.com', ['display_name' => 'Second Candidate']);
        $this->createPreference($viewer);

        $this->actingAs($viewer->user)
            ->post(sprintf('/discovery/%d/reject', $first->getKey()))
            ->assertRedirect('/discovery');

        $this->assertDatabaseHas('discovery_decisions', [
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $first->getKey(),
            'decision' => 'rejected',
        ]);

        $this->actingAs($viewer->user)
            ->get('/discovery')
            ->assertOk()
            ->assertSee($second->display_name)
            ->assertDontSee($first->display_name)
            ->assertSeeLivewire(DiscoveryCard::class);
    }

    public function test_accept_records_one_sided_connection(): void
    {
        $viewer = $this->createProfile('accept.viewer@example.com');
        $target = $this->createProfile('accept.target@example.com');
        $this->createPreference($viewer);

        $this->actingAs($viewer->user)
            ->post(sprintf('/discovery/%d/accept', $target->getKey()))
            ->assertRedirect('/discovery');

        $this->assertDatabaseHas('discovery_decisions', [
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $target->getKey(),
            'decision' => 'accepted',
        ]);

        $connection = Connection::query()->firstOrFail();
        $this->assertSame('pending', $connection->status);
        $this->assertSame('accepted', $connection->profile_one_id === $viewer->getKey() ? $connection->profile_one_decision : $connection->profile_two_decision);
        $this->assertSame(0, Conversation::query()->count());
    }

    public function test_reciprocal_accept_creates_match_and_conversation(): void
    {
        $first = $this->createProfile('match.first@example.com');
        $second = $this->createProfile('match.second@example.com');
        $this->createPreference($first);
        $this->createPreference($second);
        Event::fake([MatchCreated::class]);

        $this->actingAs($first->user)
            ->post(sprintf('/discovery/%d/accept', $second->getKey()))
            ->assertRedirect('/discovery');
        $this->actingAs($second->user)
            ->post(sprintf('/discovery/%d/accept', $first->getKey()))
            ->assertRedirect('/discovery');

        $connection = Connection::query()->firstOrFail();
        $this->assertSame('matched', $connection->status);
        $this->assertSame('accepted', $connection->profile_one_decision);
        $this->assertSame('accepted', $connection->profile_two_decision);
        $this->assertNotNull($connection->matched_at);
        $this->assertSame(1, Conversation::query()->whereBelongsTo($connection)->count());
        Event::assertDispatched(MatchCreated::class, static fn (MatchCreated $event): bool => (int) $event->connection->getKey() === (int) $connection->getKey());
    }

    public function test_livewire_reject_records_decision_and_loads_next_candidate(): void
    {
        $viewer = $this->createProfile('livewire.reject.viewer@example.com');
        $first = $this->createProfile('livewire.reject.first@example.com', ['display_name' => 'Livewire First']);
        $second = $this->createProfile('livewire.reject.second@example.com', ['display_name' => 'Livewire Second']);
        $this->createPreference($viewer);

        Livewire::actingAs($viewer->user)
            ->test(DiscoveryCard::class, ['candidate' => $first])
            ->call('reject')
            ->assertSee('Livewire Second')
            ->assertDontSee('Livewire First');

        $this->assertDatabaseHas('discovery_decisions', [
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $first->getKey(),
            'decision' => 'rejected',
        ]);

        $this->assertDatabaseMissing('discovery_decisions', [
            'viewer_profile_id' => $viewer->getKey(),
            'target_profile_id' => $second->getKey(),
        ]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createProfile(string $email, array $overrides = []): DatingProfile
    {
        $user = User::query()->create([
            'name' => $overrides['display_name'] ?? 'Discovery User',
            'email' => $email,
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        return DatingProfile::query()->create(array_merge([
            'user_id' => $user->getKey(),
            'display_name' => 'Decision Candidate',
            'birthdate' => '1992-01-01',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => 47.4979,
            'approximate_longitude' => 19.0402,
            'relationship_intent' => 'long_term',
            'bio' => 'Default bio.',
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
