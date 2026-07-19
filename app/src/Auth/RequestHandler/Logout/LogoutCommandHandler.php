<?php

declare(strict_types=1);

namespace App\Auth\RequestHandler\Logout;

use App\Auth\DomainService\LogoutService;
use App\Shared\Dto\ValueObject\Context\ContextInterface;
use App\Shared\Infrastructure\Bus\Command\CommandHandlerInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use App\Shared\Infrastructure\Bus\Command\CommandResponseInterface;
use Illuminate\Http\Request;

final readonly class LogoutCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private LogoutService $service,
        private Request $request,
    ) {
    }

    public function handle(CommandInterface $command, ContextInterface $context): ?CommandResponseInterface
    {
        if (!$command instanceof LogoutCommand) {
            throw new \InvalidArgumentException(sprintf('Expected %s.', LogoutCommand::class));
        }

        $this->service->handle($this->request);

        return null;
    }
}
