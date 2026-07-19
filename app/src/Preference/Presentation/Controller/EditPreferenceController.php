<?php

declare(strict_types=1);

namespace App\Preference\Presentation\Controller;

use App\Models\User;
use App\Preference\RequestHandler\GetPreferenceSummary\GetPreferenceSummaryQuery;
use App\Preference\RequestHandler\GetPreferenceSummary\GetPreferenceSummaryQueryResponse;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final readonly class EditPreferenceController
{
    public function __construct(private QueryBusInterface $queryBus)
    {
    }

    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $response = $this->queryBus->askPayload(GetPreferenceSummaryQuery::class, ['user_id' => $user->getKey()], new SystemContext((string) $user->getKey(), $request->ip()));

        if (!$response instanceof GetPreferenceSummaryQueryResponse) {
            throw new \RuntimeException('Unexpected preference summary response.');
        }

        return view('preference.edit', [
            'preference' => $response->preference,
            'summary' => $response->summary,
        ]);
    }
}
