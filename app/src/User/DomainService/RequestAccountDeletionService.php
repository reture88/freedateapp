<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\AuditLog;
use App\Models\User;
use App\User\Enum\AccountStatus;
use App\User\RequestHandler\RequestAccountDeletion\RequestAccountDeletionCommand;
use Illuminate\Support\Carbon;

final class RequestAccountDeletionService
{
    public function handle(RequestAccountDeletionCommand $command): User
    {
        $user = User::query()->findOrFail($command->userId);
        $before = $user->toArray();
        $user->forceFill([
            'account_status' => AccountStatus::DELETION_REQUESTED,
            'is_active' => false,
            'deletion_requested_at' => Carbon::now(),
        ])->save();
        $user->datingProfile()->update(['visibility_status' => 'hidden']);

        AuditLog::query()->create([
            'actor_user_id' => $user->getKey(),
            'action' => 'request_account_deletion',
            'target_type' => 'user',
            'target_id' => $user->getKey(),
            'before' => $before,
            'after' => $user->fresh()?->toArray(),
        ]);

        return $user;
    }
}
