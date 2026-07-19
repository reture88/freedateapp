<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ReportQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_open_reports_and_view_report_detail(): void
    {
        $admin = $this->createUser('admin@example.com', true);
        $reporter = $this->createUser('reporter@example.com');
        $openReport = Report::query()->create([
            'reporter_user_id' => $reporter->getKey(),
            'target_type' => 'profile',
            'target_id' => 123,
            'category' => 'fake_profile',
            'note' => 'Suspicious profile.',
            'status' => 'open',
        ]);
        Report::query()->create([
            'reporter_user_id' => $reporter->getKey(),
            'target_type' => 'message',
            'target_id' => 456,
            'category' => 'harassment',
            'status' => 'resolved',
        ]);

        $this->actingAs($admin)
            ->get('/admin/reports')
            ->assertOk()
            ->assertSee('Report queue')
            ->assertSee('fake_profile')
            ->assertDontSee('harassment');

        $this->actingAs($admin)
            ->get(sprintf('/admin/reports/%d', $openReport->getKey()))
            ->assertOk()
            ->assertSee('Suspicious profile.')
            ->assertSee('profile')
            ->assertSee('fake_profile');
    }

    public function test_non_admin_cannot_access_report_queue(): void
    {
        $user = $this->createUser('customer@example.com');

        $this->actingAs($user)->get('/admin/reports')->assertForbidden();
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
