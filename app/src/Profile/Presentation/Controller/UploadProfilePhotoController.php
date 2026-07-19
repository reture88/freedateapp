<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Profile\RequestHandler\UploadProfilePhoto\UploadProfilePhotoCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class UploadProfilePhotoController
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
            $this->commandBus->dispatchPayload(UploadProfilePhotoCommand::class, [
                'user_id' => $user->getKey(),
                'photo' => $request->file('photo'),
            ], new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception);
        }

        return redirect('/dating-profile/edit')->with('success', 'Profile photo uploaded.');
    }
}
