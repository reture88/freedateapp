<?php

declare(strict_types=1);

namespace App\Assistant\Dto;

final readonly class AssistantStarterContext
{
    /**
     * @param list<string> $targetVisibleFacts
     */
    public function __construct(
        public string $speakerName,
        public string $targetName,
        public array $targetVisibleFacts,
        public bool $unsafeContext,
    ) {
    }
}
