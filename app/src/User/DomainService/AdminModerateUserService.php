<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\AuditLog;
use App\Models\User;
use App\User\Enum\AccountStatus;

final class AdminModerateUserService
{
    public function handle(User $actor, User $target, string $action): User
    {
        $before = $target->toArray();

        match ($action) {
            'suspend' => $target->forceFill([
                'account_status' => AccountStatus::SUSPENDED,
                'is_active' => false,
            ])->save(),
            'ban' => $target->forceFill([
                'account_status' => AccountStatus::BANNED,
                'is_active' => false,
            ])->save(),
            'deactivate' => $target->forceFill([
                'account_status' => AccountStatus::DEACTIVATED,
                'is_active' => false,
            ])->save(),
            'restore' => $target->forceFill([
                'account_status' => AccountStatus::ACTIVE,
                'is_active' => true,
                'deletion_requested_at' => null,
            ])->save(),
            default => throw new \InvalidArgumentException('Invalid moderation action.'),
        };

        AuditLog::query()->create([
            'actor_user_id' => $actor->getKey(),
            'action' => $action,
            'target_type' => 'user',
            'target_id' => $target->getKey(),
            'before' => $before,
            'after' => $target->fresh()?->toArray(),
        ]);

        return $target;
    }
}
