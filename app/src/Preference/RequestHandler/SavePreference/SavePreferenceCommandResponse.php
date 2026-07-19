<?php

declare(strict_types=1);

namespace App\Preference\RequestHandler\SavePreference;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class SavePreferenceCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $preferenceId)
    {
    }
}
