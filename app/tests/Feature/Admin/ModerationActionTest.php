<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\DatingProfile;
use App\Models\Report;
use App\Models\User;
use App\User\Enum\AccountStatus;
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
        $this->assertSame(AccountStatus::SUSPENDED, $reportedUser->accountStatus());
        $this->assertFalse($reportedUser->is_active);

        $this->actingAs($admin)
            ->post(sprintf('/admin/users/%d/moderation', $reportedUser->getKey()), ['action' => 'ban'])
            ->assertRedirect(sprintf('/admin/users/%d', $reportedUser->getKey()));
        $reportedUser->refresh();
        $this->assertSame(AccountStatus::BANNED, $reportedUser->accountStatus());
        $this->assertFalse($reportedUser->is_active);

        $this->actingAs($admin)
            ->post(sprintf('/admin/users/%d/moderation', $reportedUser->getKey()), ['action' => 'deactivate'])
            ->assertRedirect(sprintf('/admin/users/%d', $reportedUser->getKey()));
        $reportedUser->refresh();
        $this->assertSame(AccountStatus::DEACTIVATED, $reportedUser->accountStatus());
        $this->assertFalse($reportedUser->is_active);

        $this->actingAs($admin)
            ->post(sprintf('/admin/users/%d/moderation', $reportedUser->getKey()), ['action' => 'restore'])
            ->assertRedirect(sprintf('/admin/users/%d', $reportedUser->getKey()));
        $reportedUser->refresh();
        $this->assertSame(AccountStatus::ACTIVE, $reportedUser->accountStatus());
        $this->assertTrue($reportedUser->is_active);
        $this->assertGreaterThanOrEqual(5, AuditLog::query()->count());
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $admin->getKey(),
            'action' => 'ban',
            'target_type' => 'user',
            'target_id' => $reportedUser->getKey(),
        ]);
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

    public function test_admin_user_update_writes_account_status_and_audit_log(): void
    {
        $admin = $this->createUser('account-editor@example.com', true);
        $target = $this->createUser('status-target@example.com');

        $this->actingAs($admin)
            ->post(sprintf('/admin/users/%d', $target->getKey()), [
                'role' => 'customer',
                'account_status' => AccountStatus::SUSPENDED->value,
            ])
            ->assertRedirect();

        $target->refresh();
        $this->assertSame(AccountStatus::SUSPENDED, $target->accountStatus());
        $this->assertFalse($target->is_active);
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $admin->getKey(),
            'action' => 'update_user_account',
            'target_type' => 'user',
            'target_id' => $target->getKey(),
        ]);
    }

    public function test_restoring_deletion_requested_user_clears_deletion_timestamp(): void
    {
        $admin = $this->createUser('restore-editor@example.com', true);
        $target = $this->createUser('restore-target@example.com');
        $target->forceFill([
            'account_status' => AccountStatus::DELETION_REQUESTED,
            'is_active' => false,
            'deletion_requested_at' => '2026-07-19 12:00:00',
        ])->save();

        $this->actingAs($admin)
            ->post(sprintf('/admin/users/%d/moderation', $target->getKey()), ['action' => 'restore'])
            ->assertRedirect(sprintf('/admin/users/%d', $target->getKey()));

        $target->refresh();
        $this->assertSame(AccountStatus::ACTIVE, $target->accountStatus());
        $this->assertTrue($target->is_active);
        $this->assertNull($target->deletion_requested_at);
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
            'account_status' => AccountStatus::ACTIVE,
        ]);
    }
}
