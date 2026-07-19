<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use App\Auth\DomainService\Exception\InvalidCredentialsException;
use App\Auth\RequestHandler\Login\LoginCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class LoginController
{
    use InteractsWithErrorResponses;

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        try {
            $this->commandBus->dispatchPayload(LoginCommand::class, $validated, new SystemContext());
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception, $request->only('email', 'remember'));
        } catch (InvalidCredentialsException $exception) {
            return back()->withErrors(['email' => $exception->getMessage()])->withInput($request->only('email', 'remember'));
        }

        return redirect()->intended(route('home'));
    }
}
