<?php

declare(strict_types=1);

namespace App\Chat\RequestHandler\MarkConversationRead;

use App\Chat\RequestHandler\ShowConversation\ShowConversationQuery;
use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class MarkConversationReadCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $userId,
        public int $conversationId,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self((int) $payload['user_id'], (int) $payload['conversation_id']);
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'user_id' => 'required',
            'conversation_id' => 'required',
        ];
    }

    public function toConversationQuery(): ShowConversationQuery
    {
        return new ShowConversationQuery($this->userId, $this->conversationId);
    }
}
