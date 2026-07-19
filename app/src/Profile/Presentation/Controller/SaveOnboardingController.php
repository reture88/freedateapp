<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Profile\RequestHandler\SaveDatingProfile\SaveDatingProfileCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class SaveOnboardingController
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
            $this->commandBus->dispatchPayload(SaveDatingProfileCommand::class, $payload, new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception, $request->except('privacy_consent'));
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['birthdate' => $exception->getMessage()])->withInput($request->except('privacy_consent'));
        }

        return redirect('/dating-profile/preview')->with('success', 'Dating profile completed.');
    }
}
