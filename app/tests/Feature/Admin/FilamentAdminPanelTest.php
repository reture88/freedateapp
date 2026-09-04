<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FilamentAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_filament_login(): void
    {
        $this->get('/admin-panel')->assertRedirect('/admin-panel/login');
    }

    public function test_non_admin_cannot_access_filament_panel(): void
    {
        $user = $this->createUser('filament.customer@example.com');

        $this->actingAs($user)->get('/admin-panel')->assertForbidden();
    }

    public function test_admin_can_access_filament_dashboard_and_users_resource(): void
    {
        $admin = $this->createUser('filament.admin@example.com', true);

        $this->actingAs($admin)
            ->get('/admin-panel')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin-panel/users')
            ->assertOk()
            ->assertSee('filament.admin@example.com');
    }

    private function createUser(string $email, bool $admin = false): User
    {
        return User::query()->create([
            'name' => $admin ? 'Admin User' : 'Customer User',
            'email' => $email,
            'password' => 'secret123',
            'role' => $admin ? 'admin' : 'customer',
            'is_admin' => $admin,
            'is_active' => true,
        ]);
    }
}
