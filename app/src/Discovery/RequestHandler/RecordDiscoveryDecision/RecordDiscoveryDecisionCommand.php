<?php

declare(strict_types=1);

namespace App\Discovery\RequestHandler\RecordDiscoveryDecision;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class RecordDiscoveryDecisionCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $userId,
        public int $targetProfileId,
        public string $decision,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            (int) $payload['user_id'],
            (int) $payload['target_profile_id'],
            (string) $payload['decision'],
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'user_id' => 'required',
            'target_profile_id' => 'required',
            'decision' => 'required|in:accepted,rejected',
        ];
    }
}
