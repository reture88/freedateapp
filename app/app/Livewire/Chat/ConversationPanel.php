<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Chat\Dto\ConversationView;
use App\Chat\RequestHandler\SendMessage\SendMessageCommand;
use App\Chat\RequestHandler\ShowConversation\ShowConversationQuery;
use App\Chat\RequestHandler\ShowConversation\ShowConversationQueryResponse;
use App\Models\User;
use App\Shared\Dto\ValueObject\Context\SystemContext;
use App\Shared\Infrastructure\Bus\Command\CommandBusInterface;
use App\Shared\Infrastructure\Bus\Query\QueryBusInterface;
use App\Shared\Infrastructure\Validation\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

final class ConversationPanel extends Component
{
    public int $conversationId;

    public int $connectionId;

    public string $body = '';

    /**
     * @var array{id: int, user_id: int, display_name: string, primary_photo_url: string|null}
     */
    public array $otherProfile = [
        'id' => 0,
        'user_id' => 0,
        'display_name' => '',
        'primary_photo_url' => null,
    ];

    /**
     * @var list<array{id: int, sender_type: string, body: string}>
     */
    public array $messages = [];

    public function mount(ConversationView $conversationView): void
    {
        $this->fillFromConversationView($conversationView);
    }

    public function send(CommandBusInterface $commandBus, QueryBusInterface $queryBus): void
    {
        $this->resetErrorBag();

        /** @var User|null $user */
        $user = Auth::user();

        if (null === $user) {
            $this->addError('auth', 'You must be logged in to send messages.');

            return;
        }

        $context = new SystemContext((string) $user->getKey(), request()->ip());

        try {
            $commandBus->dispatchPayload(SendMessageCommand::class, [
                'user_id' => $user->getKey(),
                'conversation_id' => $this->conversationId,
                'body' => $this->body,
            ], $context);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            return;
        } catch (\InvalidArgumentException) {
            $this->addError('conversation', 'Conversation is not available.');

            return;
        }

        $this->body = '';
        $this->refreshConversation($queryBus, $context);
    }

    public function render(): View
    {
        return view('livewire.chat.conversation-panel');
    }

    /**
     * @param array{id?: int, conversation_id?: int, sender_type?: string, body?: string} $event
     */
    #[On('echo-private:conversations.{conversationId},MessageSent')]
    public function appendBroadcastedMessage(array $event): void
    {
        $messageId = (int) ($event['id'] ?? 0);

        if ($messageId < 1 || (int) ($event['conversation_id'] ?? 0) !== $this->conversationId) {
            return;
        }

        foreach ($this->messages as $message) {
            if ($message['id'] === $messageId) {
                return;
            }
        }

        $this->messages[] = [
            'id' => $messageId,
            'sender_type' => (string) ($event['sender_type'] ?? 'user'),
            'body' => (string) ($event['body'] ?? ''),
        ];
    }

    private function refreshConversation(QueryBusInterface $queryBus, SystemContext $context): void
    {
        /** @var User $user */
        $user = Auth::user();
        $response = $queryBus->askPayload(ShowConversationQuery::class, [
            'user_id' => $user->getKey(),
            'conversation_id' => $this->conversationId,
        ], $context);

        if (!$response instanceof ShowConversationQueryResponse) {
            throw new \RuntimeException('Unexpected conversation response.');
        }

        $this->fillFromConversationView($response->conversationView);
    }

    private function fillFromConversationView(ConversationView $conversationView): void
    {
        $primaryPhoto = $conversationView->otherProfile->photos->where('is_primary', true)->sortBy('sort_order')->first()
            ?? $conversationView->otherProfile->photos->sortBy('sort_order')->first();

        $this->conversationId = (int) $conversationView->conversation->getKey();
        $this->connectionId = (int) $conversationView->conversation->connection_id;
        $this->otherProfile = [
            'id' => (int) $conversationView->otherProfile->getKey(),
            'user_id' => (int) $conversationView->otherProfile->user_id,
            'display_name' => $conversationView->otherProfile->display_name,
            'primary_photo_url' => $primaryPhoto?->publicUrl(),
        ];
        $this->messages = $conversationView->conversation->messages
            ->map(static fn ($message): array => [
                'id' => (int) $message->getKey(),
                'sender_type' => (string) $message->sender_type,
                'body' => (string) $message->body,
            ])
            ->values()
            ->all();
    }
}
