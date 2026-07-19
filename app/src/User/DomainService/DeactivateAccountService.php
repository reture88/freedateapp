<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\User;
use App\User\RequestHandler\DeactivateAccount\DeactivateAccountCommand;

final class DeactivateAccountService
{
    public function handle(DeactivateAccountCommand $command): User
    {
        $user = User::query()->findOrFail($command->userId);
        $user->forceFill(['is_active' => false])->save();
        $user->datingProfile()->update(['visibility_status' => 'hidden']);

        return $user;
    }
}
