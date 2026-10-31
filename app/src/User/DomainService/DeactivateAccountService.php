<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\AuditLog;
use App\Models\User;
use App\User\Enum\AccountStatus;
use App\User\RequestHandler\DeactivateAccount\DeactivateAccountCommand;

final class DeactivateAccountService
{
    public function handle(DeactivateAccountCommand $command): User
    {
        $user = User::query()->findOrFail($command->userId);
        $before = $user->toArray();
        $user->forceFill([
            'account_status' => AccountStatus::DEACTIVATED,
            'is_active' => false,
        ])->save();
        $user->datingProfile()->update(['visibility_status' => 'hidden']);

        AuditLog::query()->create([
            'actor_user_id' => $user->getKey(),
            'action' => 'deactivate_account',
            'target_type' => 'user',
            'target_id' => $user->getKey(),
            'before' => $before,
            'after' => $user->fresh()?->toArray(),
        ]);

        return $user;
    }
}
