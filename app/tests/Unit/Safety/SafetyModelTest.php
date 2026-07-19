<?php

declare(strict_types=1);

namespace Tests\Unit\Safety;

use App\Models\AuditLog;
use App\Models\Block;
use App\Models\MetricEvent;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SafetyModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_block_audit_log_and_metric_event_models_store_operational_data(): void
    {
        [$reporter, $target, $admin] = $this->createUsers();

        $report = Report::query()->create([
            'reporter_user_id' => $reporter->getKey(),
            'target_type' => 'profile',
            'target_id' => 123,
            'category' => 'fake_profile',
            'note' => 'Photos look copied.',
            'status' => 'open',
            'reviewer_user_id' => $admin->getKey(),
            'resolution' => null,
            'resolved_at' => null,
        ]);

        $block = Block::query()->create([
            'blocker_user_id' => $reporter->getKey(),
            'blocked_user_id' => $target->getKey(),
            'reason_category' => 'harassment',
        ]);

        $auditLog = AuditLog::query()->create([
            'actor_user_id' => $admin->getKey(),
            'action' => 'report.assigned',
            'target_type' => 'report',
            'target_id' => $report->getKey(),
            'before' => ['status' => 'open', 'reviewer_user_id' => null],
            'after' => ['status' => 'open', 'reviewer_user_id' => $admin->getKey()],
        ]);

        $metricEvent = MetricEvent::query()->create([
            'event_name' => 'report_created',
            'user_id' => $reporter->getKey(),
            'payload' => ['category' => 'fake_profile'],
            'occurred_at' => '2026-07-19 14:00:00',
        ]);

        $this->assertTrue($report->reporter()->is($reporter));
        $this->assertTrue($report->reviewer()->is($admin));
        $this->assertTrue($block->blocker()->is($reporter));
        $this->assertTrue($block->blocked()->is($target));
        $this->assertTrue($auditLog->actor()->is($admin));
        $this->assertSame(['status' => 'open', 'reviewer_user_id' => null], $auditLog->before);
        $this->assertSame(['category' => 'fake_profile'], $metricEvent->payload);
        $this->assertSame('2026-07-19 14:00:00', $metricEvent->occurred_at->format('Y-m-d H:i:s'));
    }

    public function test_block_is_unique_per_blocker_and_blocked_user_pair(): void
    {
        [$blocker, $blocked] = $this->createUsers();

        Block::query()->create([
            'blocker_user_id' => $blocker->getKey(),
            'blocked_user_id' => $blocked->getKey(),
            'reason_category' => 'spam',
        ]);

        $this->expectException(QueryException::class);

        Block::query()->create([
            'blocker_user_id' => $blocker->getKey(),
            'blocked_user_id' => $blocked->getKey(),
            'reason_category' => 'harassment',
        ]);
    }

    /**
     * @return array<int, User>
     */
    private function createUsers(): array
    {
        return [
            User::query()->create([
                'name' => 'Reporter',
                'email' => 'reporter@example.com',
                'password' => 'secret123',
                'role' => 'customer',
                'is_admin' => false,
                'is_active' => true,
            ]),
            User::query()->create([
                'name' => 'Target',
                'email' => 'target@example.com',
                'password' => 'secret123',
                'role' => 'customer',
                'is_admin' => false,
                'is_active' => true,
            ]),
            User::query()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => 'secret123',
                'role' => 'admin',
                'is_admin' => true,
                'is_active' => true,
            ]),
        ];
    }
}
