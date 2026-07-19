<?php

declare(strict_types=1);

namespace App\Safety\Presentation\Controller;

use App\Models\User;
use App\Safety\RequestHandler\BlockUser\BlockUserCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class BlockUserController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->commandBus->dispatchPayload(BlockUserCommand::class, [
                'blocker_user_id' => $user->getKey(),
                'blocked_user_id' => $request->input('blocked_user_id'),
                'reason_category' => $request->input('reason_category'),
            ], new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (\InvalidArgumentException) {
            abort(403);
        }

        return back()->with('success', 'User blocked.');
    }
}
