<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_then_login_then_logout(): void
    {
        $register = $this->post('/auth/register', [
            'name' => 'Test User',
            'email' => 'test.user@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $register->assertRedirect('/');
        $this->assertAuthenticated();

        auth()->logout();

        User::query()->create([
            'name' => 'Login User',
            'email' => 'login.user@example.com',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $login = $this->post('/auth/login', [
            'email' => 'login.user@example.com',
            'password' => 'secret123',
        ]);

        $login->assertRedirect('/');
        $this->assertAuthenticated();

        $logout = $this->post('/auth/logout');
        $logout->assertRedirect('/auth/login');
        $this->assertGuest();
    }
}
