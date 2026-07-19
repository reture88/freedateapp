<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\BlockUser;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class BlockUserCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $blockerUserId,
        public int $blockedUserId,
        public ?string $reasonCategory,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            (int) $payload['blocker_user_id'],
            (int) $payload['blocked_user_id'],
            isset($payload['reason_category']) && '' !== $payload['reason_category'] ? (string) $payload['reason_category'] : null,
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'blocker_user_id' => 'required',
            'blocked_user_id' => 'required',
            'reason_category' => 'nullable|max:60',
        ];
    }
}
