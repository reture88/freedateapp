<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Logout;

use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class LogoutCommand implements CommandInterface
{
}
