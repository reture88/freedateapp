<?php

declare(strict_types=1);

namespace App\Chat\Presentation\Controller;

use App\Chat\RequestHandler\SendMessage\SendMessageCommand;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use App\Shared\Presentation\Controller\InteractsWithErrorResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class SendMessageController
{
    use InteractsWithErrorResponses;

    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request, int $conversation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $this->commandBus->dispatchPayload(SendMessageCommand::class, [
                'user_id' => $user->getKey(),
                'conversation_id' => $conversation,
                'body' => $request->input('body'),
            ], new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (ValidationException $exception) {
            return $this->backWithValidationErrors($exception);
        } catch (\InvalidArgumentException) {
            abort(403);
        }

        return redirect(sprintf('/conversations/%d', $conversation));
    }
}
