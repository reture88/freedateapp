<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Profile\RequestHandler\ChoosePrimaryProfilePhoto\ChoosePrimaryProfilePhotoCommand;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class ChoosePrimaryProfilePhotoController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request, int $photo): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->commandBus->dispatchPayload(ChoosePrimaryProfilePhotoCommand::class, [
            'user_id' => $user->getKey(),
            'photo_id' => $photo,
        ], new SystemContext((string) $user->getKey(), $request->ip()));

        return redirect('/dating-profile/edit')->with('success', 'Primary profile photo updated.');
    }
}
