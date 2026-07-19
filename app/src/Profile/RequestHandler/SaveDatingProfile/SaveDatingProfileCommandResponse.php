<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\SaveDatingProfile;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class SaveDatingProfileCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $profileId)
    {
    }
}
