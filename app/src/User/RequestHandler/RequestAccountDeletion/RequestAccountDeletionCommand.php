<?php

declare(strict_types=1);

namespace App\User\RequestHandler\RequestAccountDeletion;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class RequestAccountDeletionCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(public int $userId)
    {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self((int) $payload['user_id']);
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return ['user_id' => 'required'];
    }
}
