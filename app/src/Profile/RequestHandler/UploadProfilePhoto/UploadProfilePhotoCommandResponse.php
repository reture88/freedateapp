<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\UploadProfilePhoto;

use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;

final readonly class UploadProfilePhotoCommandResponse implements CommandResponseInterface
{
    public function __construct(public int $photoId)
    {
    }
}
