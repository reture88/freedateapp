<?php

declare(strict_types=1);

namespace App\Profile\Presentation\Controller;

use App\Models\User;
use App\Profile\RequestHandler\ShowMatchedProfile\ShowMatchedProfileQuery;
use App\Profile\RequestHandler\ShowMatchedProfile\ShowMatchedProfileQueryResponse;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final readonly class ShowMatchedProfileController
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    public function __invoke(Request $request, int $profile): View
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $response = $this->queryBus->askPayload(ShowMatchedProfileQuery::class, [
                'user_id' => $user->getKey(),
                'profile_id' => $profile,
            ], new SystemContext((string) $user->getKey(), $request->ip()));
        } catch (\InvalidArgumentException) {
            abort(403);
        }

        if (!$response instanceof ShowMatchedProfileQueryResponse) {
            throw new \RuntimeException('Unexpected matched profile response.');
        }

        return view('profile.show', ['profile' => $response->profile]);
    }
}
