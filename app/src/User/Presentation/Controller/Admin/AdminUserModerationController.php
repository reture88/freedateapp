<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Admin;

use App\Models\User;
use App\User\DomainService\AdminModerateUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class AdminUserModerationController
{
    public function __construct(private AdminModerateUserService $service)
    {
    }

    public function __invoke(Request $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $this->service->handle($actor, $user, (string) $request->input('action'));

        return redirect(route('admin.users.show', $user));
    }
}
