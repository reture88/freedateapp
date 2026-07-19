<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use App\Auth\RequestHandler\Logout\LogoutCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use Illuminate\Http\RedirectResponse;

final readonly class LogoutController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(): RedirectResponse
    {
        $this->commandBus->dispatch(new LogoutCommand(), new SystemContext());

        return redirect()->route('auth.login.show-form');
    }
}
