<?php

declare(strict_types=1);

namespace App\User\Presentation\Controller;

use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use App\User\DomainService\Exception\InvalidAddressTypeException;
use App\User\RequestHandler\SaveAddress\SaveAddressCommand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class SaveAddressController
{
    use InteractsWithErrorResponses;

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request, string $type): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|size:2',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        try {
            $this->commandBus->dispatchPayload(
                SaveAddressCommand::class,
                array_merge($validated, [
                    'type' => $type,
                    'user' => $request->user(),
                ]),
                new SystemContext((string) $request->user()?->getAuthIdentifier(), $request->ip()),
            );
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception);
        } catch (InvalidAddressTypeException $exception) {
            return $this->backWithMessageError('address', $exception->getMessage());
        }

        return back()->with('success', 'Address saved.');
    }
}
