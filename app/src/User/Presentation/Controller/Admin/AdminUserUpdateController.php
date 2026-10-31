<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Admin;

use App\Models\AuditLog;
use App\Models\User;
use App\User\Enum\AccountStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AdminUserUpdateController
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => 'required|string|in:admin,customer',
            'account_status' => 'required|string|in:active,suspended,banned,deactivated,deletion_requested',
        ]);

        $role = (string) $data['role'];
        $accountStatus = AccountStatus::from((string) $data['account_status']);
        $before = $user->toArray();

        $user->forceFill([
            'role' => $role,
            'is_admin' => 'admin' === $role,
            'account_status' => $accountStatus,
            'is_active' => $accountStatus->canUseProduct(),
            'deletion_requested_at' => AccountStatus::DELETION_REQUESTED === $accountStatus
                ? ($user->deletion_requested_at ?? now())
                : null,
        ])->save();

        AuditLog::query()->create([
            'actor_user_id' => $request->user()?->getKey(),
            'action' => 'update_user_account',
            'target_type' => 'user',
            'target_id' => $user->getKey(),
            'before' => $before,
            'after' => $user->fresh()?->toArray(),
        ]);

        return back()->with('success', 'User updated.');
    }
}
