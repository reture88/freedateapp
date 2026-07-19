<?php

declare(strict_types=1);

namespace App\Safety\RequestHandler\SubmitReport;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class SubmitReportCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $reporterUserId,
        public string $targetType,
        public int $targetId,
        public string $category,
        public ?string $note,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            (int) $payload['reporter_user_id'],
            (string) $payload['target_type'],
            (int) $payload['target_id'],
            (string) $payload['category'],
            isset($payload['note']) && '' !== $payload['note'] ? (string) $payload['note'] : null,
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'reporter_user_id' => 'required',
            'target_type' => 'required|in:profile,message,assistant_message',
            'target_id' => 'required',
            'category' => 'required|max:60',
            'note' => 'nullable|max:2000',
        ];
    }
}
