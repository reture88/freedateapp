<?php

declare(strict_types=1);

namespace App\Preference\RequestHandler\GetPreferenceSummary;

use App\Models\ProfilePreference;
use App\Preference\Dto\PreferenceSummary;
use App\Shared\Infrastructure\Bus\Query\QueryResponseInterface;

final readonly class GetPreferenceSummaryQueryResponse implements QueryResponseInterface
{
    public function __construct(
        public ProfilePreference $preference,
        public PreferenceSummary $summary,
    ) {
    }
}
