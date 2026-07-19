<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\ChoosePrimaryProfilePhoto;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class ChoosePrimaryProfilePhotoCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $photoId)
    {
    }
}
