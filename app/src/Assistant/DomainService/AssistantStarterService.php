<?php

declare(strict_types=1);

namespace App\Assistant\DomainService;

use App\Assistant\Dto\AssistantStarterContext;

final class AssistantStarterService
{
    public function handle(AssistantStarterContext $context): ?string
    {
        if ($context->unsafeContext) {
            return null;
        }

        if ([] === $context->targetVisibleFacts) {
            return sprintf(
                '%s, %s mar itt van a matchben, ugyhogy a vilag legegyszerubb nyito kerdese kovetkezik: hogy indult a napod?',
                $context->speakerName,
                $context->targetName,
            );
        }

        return sprintf(
            '%s, lattad, hogy %s profiljan %s is vannak? En a helyedben megkerdeznem, hogy a kovetkezo seta csapatprogram-e. Beszelgessetek fiatalok.',
            $context->speakerName,
            $context->targetName,
            $this->humanizeFacts($context->targetVisibleFacts),
        );
    }

    /**
     * @param list<string> $facts
     */
    private function humanizeFacts(array $facts): string
    {
        $selectedFacts = array_slice($facts, 0, 2);
        $translatedFacts = array_map(static fn (string $fact): string => match ($fact) {
            'three cats' => 'harom macska',
            'weekend hikes' => 'hetvegi turak',
            default => $fact,
        }, $selectedFacts);

        return implode(' es ', $translatedFacts);
    }
}
