<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\DatingProfile;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ModerationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_resolve_report_hide_profile_suspend_and_restore_user(): void
    {
        $admin = $this->createUser('moderator@example.com', true);
        $reportedUser = $this->createUser('reported@example.com');
        $profile = DatingProfile::query()->create([
            'user_id' => $reportedUser->getKey(),
            'display_name' => 'Reported Profile',
            'birthdate' => '1992-01-01',
            'gender' => 'man',
            'orientation' => 'straight',
            'city' => 'Budapest',
            'country_code' => 'HU',
            'relationship_intent' => 'long_term',
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => false,
            'completed_at' => '2026-07-19 12:00:00',
        ]);
        $report = Report::query()->create([
            'reporter_user_id' => $admin->getKey(),
            'target_type' => 'profile',
            'target_id' => $profile->getKey(),
            'category' => 'fake_profile',
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->post(sprintf('/admin/reports/%d/actions', $report->getKey()), [
                'action' => 'hide_profile',
                'resolution' => 'profile_hidden',
            ])
            ->assertRedirect(sprintf('/admin/reports/%d', $report->getKey()));

        $report->refresh();
        $profile->refresh();
        $this->assertSame('resolved', $report->status);
        $this->assertSame($admin->getKey(), $report->reviewer_user_id);
        $this->assertSame('profile_hidden', $report->resolution);
        $this->assertSame('hidden', $profile->visibility_status);
        $this->assertSame('rejected', $profile->moderation_status);
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $admin->getKey(),
            'action' => 'hide_profile',
            'target_type' => 'report',
            'target_id' => $report->getKey(),
        ]);

        $this->actingAs($admin)
            ->post(sprintf('/admin/users/%d/moderation', $reportedUser->getKey()), ['action' => 'suspend'])
            ->assertRedirect(sprintf('/admin/users/%d', $reportedUser->getKey()));
        $reportedUser->refresh();
        $this->assertFalse($reportedUser->is_active);

        $this->actingAs($admin)
            ->post(sprintf('/admin/users/%d/moderation', $reportedUser->getKey()), ['action' => 'restore'])
            ->assertRedirect(sprintf('/admin/users/%d', $reportedUser->getKey()));
        $reportedUser->refresh();
        $this->assertTrue($reportedUser->is_active);
        $this->assertGreaterThanOrEqual(3, AuditLog::query()->count());
    }

    public function test_non_admin_cannot_run_moderation_actions(): void
    {
        $user = $this->createUser('moderation.customer@example.com');
        $report = Report::query()->create([
            'reporter_user_id' => $user->getKey(),
            'target_type' => 'profile',
            'target_id' => 123,
            'category' => 'fake_profile',
            'status' => 'open',
        ]);

        $this->actingAs($user)
            ->post(sprintf('/admin/reports/%d/actions', $report->getKey()), ['action' => 'resolve'])
            ->assertForbidden();
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
