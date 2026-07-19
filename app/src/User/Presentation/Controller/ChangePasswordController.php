<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use App\User\DomainService\Exception\CurrentPasswordMismatchException;
use App\User\DomainService\Exception\PasswordConfirmationMismatchException;
use App\User\RequestHandler\ChangePassword\ChangePasswordCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class ChangePasswordController
{
    use InteractsWithErrorResponses;

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string',
        ]);

        try {
            $this->commandBus->dispatchPayload(
                ChangePasswordCommand::class,
                array_merge($validated, ['user' => $request->user()]),
                new SystemContext((string) $request->user()?->getAuthIdentifier(), $request->ip()),
            );
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception);
        } catch (CurrentPasswordMismatchException|PasswordConfirmationMismatchException|\InvalidArgumentException $exception) {
            return $this->backWithMessageError('password', $exception->getMessage());
        }

        return back()->with('success', 'Password changed.');
    }
}
