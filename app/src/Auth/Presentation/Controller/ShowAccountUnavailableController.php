<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use App\Models\User;
use App\User\Enum\AccountStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ShowAccountUnavailableController
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $status = $user instanceof User ? $user->accountStatus() : null;

        return view('auth.account-unavailable', [
            'status' => $status,
            'headline' => $this->headline($status),
            'message' => $this->message($status),
        ]);
    }

    private function headline(?AccountStatus $status): string
    {
        return match ($status) {
            AccountStatus::SUSPENDED => 'Your account is suspended',
            AccountStatus::BANNED => 'Your account is banned',
            AccountStatus::DEACTIVATED => 'Your account is deactivated',
            AccountStatus::DELETION_REQUESTED => 'Account deletion is pending',
            default => 'Access is currently unavailable',
        };
    }

    private function message(?AccountStatus $status): string
    {
        return match ($status) {
            AccountStatus::SUSPENDED => 'Your account is temporarily restricted and cannot use MTAM product features right now.',
            AccountStatus::BANNED => 'Your account is permanently restricted from normal MTAM product access.',
            AccountStatus::DEACTIVATED => 'Your account has been deactivated and cannot use MTAM product features.',
            AccountStatus::DELETION_REQUESTED => 'Your deletion request is being processed, so normal account access is unavailable.',
            default => 'Your account is not active right now. You can return to sign in or contact support if you think this is unexpected.',
        };
    }
}
