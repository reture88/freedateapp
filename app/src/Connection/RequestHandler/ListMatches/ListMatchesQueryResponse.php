<?php

declare(strict_types=1);

namespace App\Connection\RequestHandler\ListMatches;

use App\Connection\Dto\MatchListItem;
use App\Shared\Infrastructure\Bus\Query\QueryResponseInterface;

final readonly class ListMatchesQueryResponse implements QueryResponseInterface
{
    /**
     * @param list<MatchListItem> $matches
     */
    public function __construct(public array $matches)
    {
    }
}
