<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Connection\Dto\MatchListItem;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class MessageList extends Component
{
    /**
     * @var list<array{conversation_id: int, display_name: string, age: int|null, primary_photo_url: string|null, matched_at: string|null, unread_count: int, latest_message_preview: string|null, latest_message_at: string|null, state: string, preview_label: string}>
     */
    public array $conversations = [];

    /**
     * @param list<MatchListItem> $conversations
     */
    public function mount(array $conversations): void
    {
        $this->conversations = array_map(fn (MatchListItem $conversation): array => [
            'conversation_id' => $conversation->conversationId,
            'display_name' => $conversation->displayName,
            'age' => $conversation->age,
            'primary_photo_url' => $conversation->primaryPhotoUrl,
            'matched_at' => $conversation->matchedAt,
            'unread_count' => $conversation->unreadCount,
            'latest_message_preview' => $conversation->latestMessagePreview,
            'latest_message_at' => $conversation->latestMessageAt,
            'state' => $conversation->unreadCount > 0 ? 'unread' : 'default',
            'preview_label' => $this->previewLabel($conversation),
        ], $conversations);

        usort($this->conversations, static function (array $first, array $second): int {
            return strcmp($second['latest_message_at'] ?? $second['matched_at'] ?? '', $first['latest_message_at'] ?? $first['matched_at'] ?? '');
        });
    }

    public function render(): View
    {
        return view('livewire.chat.message-list');
    }

    private function previewLabel(MatchListItem $conversation): string
    {
        if (null !== $conversation->latestMessagePreview && '' !== trim($conversation->latestMessagePreview)) {
            return trim($conversation->latestMessagePreview);
        }

        if (null !== $conversation->matchedAt && CarbonImmutable::parse($conversation->matchedAt)->greaterThanOrEqualTo(CarbonImmutable::now()->subDays(7))) {
            return 'Matched recently';
        }

        return 'No messages yet';
    }
}
