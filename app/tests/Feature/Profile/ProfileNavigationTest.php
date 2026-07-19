<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProfileNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_profile_links_to_dating_profile_photo_management(): void
    {
        $user = User::query()->create([
            'name' => 'Navigation User',
            'email' => 'navigation@example.com',
            'password' => 'secret123',
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Tarskereso profil')
            ->assertSee('/dating-profile/edit');
    }
}
