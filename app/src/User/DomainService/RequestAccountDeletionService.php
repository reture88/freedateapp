<?php

declare(strict_types=1);

namespace App\User\DomainService;

use App\Models\User;
use App\User\RequestHandler\RequestAccountDeletion\RequestAccountDeletionCommand;
use Illuminate\Support\Carbon;

final class RequestAccountDeletionService
{
    public function handle(RequestAccountDeletionCommand $command): User
    {
        $user = User::query()->findOrFail($command->userId);
        $user->forceFill(['deletion_requested_at' => Carbon::now()])->save();
        $user->datingProfile()->update(['visibility_status' => 'hidden']);

        return $user;
    }
}
