<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\AuditLog;
use App\Models\User;

final class AdminModerateUserService
{
    public function handle(User $actor, User $target, string $action): User
    {
        $before = $target->toArray();

        match ($action) {
            'suspend', 'ban' => $target->forceFill(['is_active' => false])->save(),
            'restore' => $target->forceFill(['is_active' => true])->save(),
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
