<?php

declare(strict_types=1);

namespace App\Preference\Presentation\Controller;

use App\Models\User;
use App\Preference\RequestHandler\SavePreference\SavePreferenceCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class SavePreferenceController
{
    use InteractsWithErrorResponses;

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $payload = $request->all();
        $payload['user_id'] = $user->getKey();

        try {
            $this->commandBus->dispatchPayload(SavePreferenceCommand::class, $payload, new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception);
        }

        return redirect('/dating-preferences/edit')->with('success', 'Dating preferences saved.');
    }
}
