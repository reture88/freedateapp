<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\DatingProfile;
use App\Models\ProfileTag;
use App\Models\User;
use Database\Seeders\ProfileTagTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProfileTagSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_profile_tags_with_visibility(): void
    {
        $this->seed(ProfileTagTaxonomySeeder::class);
        $user = $this->createUser();
        $coffee = ProfileTag::query()->where('slug', 'coffee')->firstOrFail();
        $hiking = ProfileTag::query()->where('slug', 'hiking')->firstOrFail();
        $cats = ProfileTag::query()->where('slug', 'cats')->firstOrFail();

        $this->actingAs($user)
            ->post('/dating-profile/onboarding', [
                'display_name' => 'Tagged User',
                'birthdate' => '1992-03-12',
                'gender' => 'woman',
                'orientation' => 'straight',
                'city' => 'Budapest',
                'country_code' => 'HU',
                'relationship_intent' => 'long_term',
                'bio' => 'Bemutatkozo szoveg.',
                'privacy_consent' => '1',
                'profile_tags' => [
                    (string) $coffee->getKey() => 'public',
                    (string) $hiking->getKey() => 'matches_only',
                    (string) $cats->getKey() => 'private_matching',
                ],
            ])
            ->assertRedirect('/dating-profile/preview');

        $profile = DatingProfile::query()->whereBelongsTo($user)->firstOrFail();

        $this->assertDatabaseHas('dating_profile_tags', [
            'dating_profile_id' => $profile->getKey(),
            'profile_tag_id' => $coffee->getKey(),
            'visibility' => 'public',
        ]);
        $this->assertDatabaseHas('dating_profile_tags', [
            'dating_profile_id' => $profile->getKey(),
            'profile_tag_id' => $hiking->getKey(),
            'visibility' => 'matches_only',
        ]);
        $this->assertDatabaseHas('dating_profile_tags', [
            'dating_profile_id' => $profile->getKey(),
            'profile_tag_id' => $cats->getKey(),
            'visibility' => 'private_matching',
        ]);

        $this->actingAs($user)
            ->get('/dating-profile/edit')
            ->assertOk()
            ->assertSee('Profile interests')
            ->assertSee('Coffee');
    }

    private function createUser(): User
    {
        return User::query()->create([
            'name' => 'Tagged User',
            'email' => 'tagged@example.com',
            'email_verified_at' => now(),
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);
    }
}
