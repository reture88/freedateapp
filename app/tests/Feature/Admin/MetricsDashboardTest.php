<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\MetricEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class MetricsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_metrics_dashboard(): void
    {
        $admin = $this->createUser('metrics.admin@example.com', true);

        foreach ([
            'registration_completed',
            'profile_completed',
            'active_user',
            'match_created',
            'first_message_sent',
            'report_submitted',
            'block_created',
            'unmatch_created',
            'assistant_starter_posted',
            'assistant_starter_posted',
        ] as $eventName) {
            MetricEvent::query()->create([
                'event_name' => $eventName,
                'payload' => [],
                'occurred_at' => '2026-07-19 12:00:00',
            ]);
        }

        $this->actingAs($admin)
            ->get('/admin/metrics')
            ->assertOk()
            ->assertSee('Metrics dashboard')
            ->assertSee('Registrations')
            ->assertSee('Completed profiles')
            ->assertSee('Matches')
            ->assertSee('Assistant starters')
            ->assertSee('2');
    }

    public function test_non_admin_cannot_view_metrics_dashboard(): void
    {
        $user = $this->createUser('metrics.customer@example.com');

        $this->actingAs($user)->get('/admin/metrics')->assertForbidden();
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
