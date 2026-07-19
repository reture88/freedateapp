<?php

declare(strict_types=1);

namespace App\Discovery\RequestHandler\FindNextCandidate;

use App\Models\DatingProfile;
use App\Shared\Infrastructure\Bus\Query\QueryResponseInterface;

final readonly class FindNextCandidateQueryResponse implements QueryResponseInterface
{
    public function __construct(public ?DatingProfile $candidate)
    {
    }
}
