<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Assistant\DomainService\PostAssistantStartersService;
use Illuminate\Console\Command;

final class PostAiWingmanStartersCommand extends Command
{
    protected $signature = 'assistant:post-wingman-starters';

    protected $description = 'Post AI Szarnyseged starter messages for eligible empty matched conversations.';

    public function handle(PostAssistantStartersService $service): int
    {
        $posted = $service->handle();
        $this->info(sprintf('Posted %d AI Szarnyseged starter messages.', $posted));

        return self::SUCCESS;
    }
}
