<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\AdminModerateReport;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class AdminModerateReportCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $actorUserId,
        public int $reportId,
        public string $action,
        public ?string $resolution,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            (int) $payload['actor_user_id'],
            (int) $payload['report_id'],
            (string) $payload['action'],
            isset($payload['resolution']) && '' !== $payload['resolution'] ? (string) $payload['resolution'] : null,
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'actor_user_id' => 'required',
            'report_id' => 'required',
            'action' => 'required|in:resolve,dismiss,hide_profile',
            'resolution' => 'nullable|max:2000',
        ];
    }
}
