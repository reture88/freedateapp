<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller\Admin;

use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\User\RequestHandler\AdminModerateUser\AdminModerateUserCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class AdminUserModerationController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request, User $user): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $this->commandBus->dispatchPayload(AdminModerateUserCommand::class, [
            'actor_user_id' => $actor->getKey(),
            'target_user_id' => $user->getKey(),
            'action' => $request->input('action'),
        ], new SystemContext((string) $actor->getKey(), $request->ip()));

        return redirect(route('admin.users.show', $user));
    }
}
