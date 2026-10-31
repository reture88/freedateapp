<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Livewire\Profile\DatingProfileForm;
use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class ProfileOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_authenticated_user_can_complete_dating_profile_onboarding(): void
    {
        $user = User::query()->create([
            'name' => 'Onboarding User',
            'email' => 'onboarding@example.com',
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/dating-profile/onboarding')
            ->assertOk()
            ->assertSee('Dating profile onboarding')
            ->assertSeeLivewire(DatingProfileForm::class);

        $response = $this->actingAs($user)->post('/dating-profile/onboarding', [
            'display_name' => 'Rafi',
            'birthdate' => '1990-05-20',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => '47.497900',
            'approximate_longitude' => '19.040200',
            'relationship_intent' => 'long_term',
            'bio' => 'Coffee, walks, and dry humor.',
            'ai_wingman_enabled' => '1',
            'privacy_consent' => '1',
        ]);

        $response->assertRedirect('/dating-profile/preview');

        $profile = DatingProfile::query()->whereBelongsTo($user)->firstOrFail();

        $this->assertSame('Rafi', $profile->display_name);
        $this->assertSame('visible', $profile->visibility_status);
        $this->assertSame('pending', $profile->moderation_status);
        $this->assertTrue($profile->ai_wingman_enabled);
        $this->assertNotNull($profile->completed_at);

        $this->actingAs($user)
            ->get('/dating-profile/preview')
            ->assertOk()
            ->assertSee('Rafi')
            ->assertSee('Coffee, walks, and dry humor.')
            ->assertSee('Edit dating preferences');
    }

    public function test_incomplete_or_invalid_profile_data_is_rejected(): void
    {
        $user = User::query()->create([
            'name' => 'Invalid User',
            'email' => 'invalid@example.com',
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->from('/dating-profile/onboarding')
            ->post('/dating-profile/onboarding', [
                'display_name' => '',
                'birthdate' => '2012-01-01',
                'privacy_consent' => '0',
            ])
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors(['display_name', 'birthdate', 'gender', 'orientation', 'privacy_consent']);

        $this->assertDatabaseCount('dating_profiles', 0);
    }

    public function test_exactly_eighteen_year_old_user_can_complete_onboarding(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-08 12:00:00'));
        $user = $this->createUser('exactly.18@example.com');

        $this->actingAs($user)
            ->post('/dating-profile/onboarding', $this->validProfilePayload('2008-09-08'))
            ->assertRedirect('/dating-profile/preview');

        $profile = DatingProfile::query()->where('user_id', $user->getKey())->firstOrFail();
        $this->assertSame('2008-09-08', $profile->birthdate->toDateString());
    }

    public function test_one_day_under_eighteen_is_rejected(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-08 12:00:00'));
        $user = $this->createUser('underage@example.com');

        $this->actingAs($user)
            ->from('/dating-profile/onboarding')
            ->post('/dating-profile/onboarding', $this->validProfilePayload('2008-09-09'))
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors('birthdate');

        $this->assertDatabaseMissing('dating_profiles', ['user_id' => $user->getKey()]);
    }

    public function test_future_and_invalid_birthdates_are_rejected(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-08 12:00:00'));
        $futureUser = $this->createUser('future.birthdate@example.com');
        $invalidUser = $this->createUser('invalid.birthdate@example.com');

        $this->actingAs($futureUser)
            ->from('/dating-profile/onboarding')
            ->post('/dating-profile/onboarding', $this->validProfilePayload('2026-09-09'))
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors('birthdate');

        $this->actingAs($invalidUser)
            ->from('/dating-profile/onboarding')
            ->post('/dating-profile/onboarding', $this->validProfilePayload('not-a-date'))
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors('birthdate');
    }

    public function test_birthdate_update_revalidates_adult_eligibility(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-08 12:00:00'));
        $user = $this->createUser('dob.update@example.com');
        $profile = DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Adult User',
            'birthdate' => '1990-05-20',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-09-08 10:00:00',
        ]);

        $this->actingAs($user)
            ->from('/dating-profile/edit')
            ->post('/dating-profile/edit', $this->validProfilePayload('2008-09-09'))
            ->assertRedirect('/dating-profile/edit')
            ->assertSessionHasErrors('birthdate');

        $this->assertSame('1990-05-20', $profile->fresh()?->birthdate->toDateString());
    }

    public function test_legacy_underage_profile_cannot_access_dating_product_routes(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-09-08 12:00:00'));
        $underage = $this->createProfile('legacy.underage@example.com', '2008-09-09');
        $adult = $this->createProfile('legacy.adult@example.com', '1990-05-20');
        $connection = Connection::createBetween($underage, $adult, [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => '2026-09-08 11:00:00',
        ]);
        $conversation = Conversation::query()->create(['connection_id' => $connection->getKey()]);

        $this->actingAs($underage->user)
            ->get('/discovery')
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors('birthdate');

        $this->actingAs($underage->user)
            ->get('/matches')
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors('birthdate');

        $this->actingAs($underage->user)
            ->get('/messages')
            ->assertRedirect('/dating-profile/onboarding')
            ->assertSessionHasErrors('birthdate');

        $this->actingAs($underage->user)
            ->get(sprintf('/conversations/%d', $conversation->getKey()))
            ->assertForbidden();

        $this->actingAs($underage->user)
            ->get(sprintf('/profiles/%d', $adult->getKey()))
            ->assertForbidden();
    }

    public function test_livewire_profile_form_saves_through_command_bus(): void
    {
        $user = User::query()->create([
            'name' => 'Livewire Profile User',
            'email' => 'livewire.profile@example.com',
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        Livewire::actingAs($user)
            ->test(DatingProfileForm::class)
            ->set('displayName', 'Live Rafi')
            ->set('birthdate', '1991-04-22')
            ->set('gender', 'man')
            ->set('orientation', 'straight')
            ->set('city', 'Budapest')
            ->set('countryCode', 'HU')
            ->set('relationshipIntent', 'long_term')
            ->set('bio', 'Livewire saved profile.')
            ->set('aiWingmanEnabled', true)
            ->set('privacyConsent', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect('/dating-profile/preview');

        $profile = DatingProfile::query()->whereBelongsTo($user)->firstOrFail();

        $this->assertSame('Live Rafi', $profile->display_name);
        $this->assertSame('Budapest', $profile->city);
        $this->assertTrue($profile->ai_wingman_enabled);
    }

    private function createUser(string $email): User
    {
        return User::query()->create([
            'name' => 'Onboarding User',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);
    }

    private function createProfile(string $email, string $birthdate): DatingProfile
    {
        $user = $this->createUser($email);

        return DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Eligibility User',
            'birthdate' => $birthdate,
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-09-08 10:00:00',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validProfilePayload(string $birthdate): array
    {
        return [
            'display_name' => 'Adult User',
            'birthdate' => $birthdate,
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => '47.497900',
            'approximate_longitude' => '19.040200',
            'relationship_intent' => 'long_term',
            'bio' => 'Coffee, walks, and dry humor.',
            'ai_wingman_enabled' => '0',
            'privacy_consent' => '1',
        ];
    }
}
