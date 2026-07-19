<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;

final class AdminUserShowController
{
    public function __invoke(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->loadCount(['addresses']),
        ]);
    }
}
