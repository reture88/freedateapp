<?php

declare(strict_types=1);

namespace Tests\Unit\Profile;

use App\Models\DatingProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DatingProfileModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_one_dating_profile(): void
    {
        $user = User::query()->create([
            'name' => 'Profile Owner',
            'email' => 'profile.owner@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $profile = DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Rafi',
            'birthdate' => '1990-05-20',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'approximate_latitude' => 47.4979,
            'approximate_longitude' => 19.0402,
            'relationship_intent' => 'long_term',
            'bio' => 'Coffee, walks, and dry humor.',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => true,
            'completed_at' => '2026-07-19 12:00:00',
        ]);

        $this->assertTrue($user->datingProfile()->is($profile));
        $this->assertTrue($profile->user()->is($user));
    }

    public function test_dating_profile_casts_dates_coordinates_and_ai_wingman_flag(): void
    {
        $user = User::query()->create([
            'name' => 'Cast Owner',
            'email' => 'cast.owner@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $profile = DatingProfile::query()->create([
            'user_id' => $user->getKey(),
            'display_name' => 'Mari',
            'birthdate' => '1993-03-14',
            'gender' => 'woman',
            'orientation' => 'straight',
            'city' => 'Szeged',
            'country_code' => 'HU',
            'approximate_latitude' => '46.2530',
            'approximate_longitude' => '20.1414',
            'relationship_intent' => 'long_term',
            'bio' => 'Three cats and one suspiciously dramatic plant.',
            'visibility_status' => 'hidden',
            'moderation_status' => 'pending',
            'ai_wingman_enabled' => '1',
            'completed_at' => null,
        ]);

        $this->assertSame('1993-03-14', $profile->birthdate->toDateString());
        $this->assertSame(46.2530, $profile->approximate_latitude);
        $this->assertSame(20.1414, $profile->approximate_longitude);
        $this->assertTrue($profile->ai_wingman_enabled);
        $this->assertNull($profile->completed_at);
    }
}
