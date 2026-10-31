<?php

declare(strict_types=1);

namespace App\User\RequestHandler\AdminModerateUser;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class AdminModerateUserCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $actorUserId,
        public int $targetUserId,
        public string $action,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self((int) $payload['actor_user_id'], (int) $payload['target_user_id'], (string) $payload['action']);
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'actor_user_id' => 'required',
            'target_user_id' => 'required',
            'action' => 'required|in:suspend,ban,restore,deactivate',
        ];
    }
}
