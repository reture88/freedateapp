<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\User\RequestHandler\DeactivateAccount\DeactivateAccountCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final readonly class DeactivateAccountController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->commandBus->dispatchPayload(DeactivateAccountCommand::class, ['user_id' => $user->getKey()], new SystemContext((string) $user->getKey(), $request->ip()));
        Auth::logout();

        return redirect('/');
    }
}
