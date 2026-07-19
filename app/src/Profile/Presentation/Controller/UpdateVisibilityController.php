<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Profile\RequestHandler\UpdateProfileVisibility\UpdateProfileVisibilityCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class UpdateVisibilityController
{
    use InteractsWithErrorResponses;

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->commandBus->dispatchPayload(UpdateProfileVisibilityCommand::class, [
                'user_id' => $user->getKey(),
                'visibility_status' => $request->input('visibility_status'),
            ], new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception);
        } catch (\InvalidArgumentException $exception) {
            return back()->withErrors(['visibility_status' => $exception->getMessage()]);
        }

        return redirect('/dating-profile/preview')->with('success', 'Profile visibility updated.');
    }
}
