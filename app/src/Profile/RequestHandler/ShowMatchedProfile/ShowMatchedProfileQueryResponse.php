<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\ShowMatchedProfile;

use App\Models\DatingProfile;
use App\Shared\Infrastructure\Bus\Query\QueryResponseInterface;

final readonly class ShowMatchedProfileQueryResponse implements QueryResponseInterface
{
    public function __construct(public DatingProfile $profile)
    {
    }
}
