<?php

declare(strict_types=1);

namespace App\Discovery\Presentation\Controller;

use App\Discovery\RequestHandler\RecordDiscoveryDecision\RecordDiscoveryDecisionCommand;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class AcceptDiscoveryController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request, int $targetProfile): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->commandBus->dispatchPayload(RecordDiscoveryDecisionCommand::class, [
            'user_id' => $user->getKey(),
            'target_profile_id' => $targetProfile,
            'decision' => 'accepted',
        ], new SystemContext((string) $user->getKey(), $request->ip()));

        return redirect('/discovery');
    }
}
