<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Livewire\Profile\DatingProfileForm;
use App\Models\DatingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

final class ProfileOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_complete_dating_profile_onboarding(): void
    {
        $user = User::query()->create([
            'name' => 'Onboarding User',
            'email' => 'onboarding@example.com',
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
            ->assertSessionHasErrors(['display_name', 'gender', 'orientation', 'privacy_consent']);

        $this->assertDatabaseCount('dating_profiles', 0);
    }

    public function test_livewire_profile_form_saves_through_command_bus(): void
    {
        $user = User::query()->create([
            'name' => 'Livewire Profile User',
            'email' => 'livewire.profile@example.com',
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
}
