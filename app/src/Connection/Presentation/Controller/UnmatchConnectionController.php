<?php

declare(strict_types=1);

namespace App\Connection\Presentation\Controller;

use App\Connection\RequestHandler\UnmatchConnection\UnmatchConnectionCommand;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class UnmatchConnectionController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request, int $connection): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->commandBus->dispatchPayload(UnmatchConnectionCommand::class, [
                'user_id' => $user->getKey(),
                'connection_id' => $connection,
            ], new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (\InvalidArgumentException) {
            abort(403);
        }

        return redirect('/matches');
    }
}
