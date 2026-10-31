<?php

declare(strict_types=1);

namespace App\Auth\Presentation\Controller;

use App\Auth\DomainService\Exception\EmailAlreadyRegisteredException;
use App\Auth\DomainService\Exception\PasswordConfirmationException;
use App\Auth\RequestHandler\Register\RegisterCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class RegisterController
{
    use InteractsWithErrorResponses;

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string',
        ]);

        try {
            $this->commandBus->dispatchPayload(RegisterCommand::class, $validated, new SystemContext());
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception, $request->only('name', 'email'));
        } catch (EmailAlreadyRegisteredException|PasswordConfirmationException|\InvalidArgumentException $exception) {
            return back()
                ->withErrors(['email' => $exception->getMessage()])
                ->withInput($request->only('name', 'email'));
        }

        return redirect()->route('verification.notice')->with('success', 'Account created.');
    }
}
