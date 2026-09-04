<?php

declare(strict_types=1);

namespace App\Livewire\Connection;

use App\Connection\Dto\MatchListItem;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class MatchList extends Component
{
    /**
     * @var list<array{connection_id: int, conversation_id: int, display_name: string, city: string, matched_at: string|null}>
     */
    public array $matches = [];

    /**
     * @param list<MatchListItem> $matches
     */
    public function mount(array $matches): void
    {
        $this->matches = array_map(static fn (MatchListItem $match): array => [
            'connection_id' => $match->connectionId,
            'conversation_id' => $match->conversationId,
            'display_name' => $match->displayName,
            'city' => $match->city,
            'matched_at' => $match->matchedAt,
        ], $matches);
    }

    public function render(): View
    {
        return view('livewire.connection.match-list');
    }
}
