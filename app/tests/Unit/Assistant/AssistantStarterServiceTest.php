<?php

declare(strict_types=1);

namespace Tests\Unit\Assistant;

use App\Assistant\DomainService\AssistantStarterService;
use App\Assistant\Dto\AssistantStarterContext;
use PHPUnit\Framework\TestCase;

final class AssistantStarterServiceTest extends TestCase
{
    public function test_service_builds_safe_humorous_starter_from_visible_profile_facts(): void
    {
        $message = (new AssistantStarterService())->handle(new AssistantStarterContext(
            speakerName: 'Sanyi',
            targetName: 'Mari',
            targetVisibleFacts: ['three cats', 'weekend hikes'],
            unsafeContext: false,
        ));

        $this->assertSame(
            'Sanyi, lattad, hogy Mari profiljan harom macska es hetvegi turak is vannak? En a helyedben megkerdeznem, hogy a kovetkezo seta csapatprogram-e. Beszelgessetek fiatalok.',
            $message,
        );
    }

    public function test_service_never_uses_missing_facts(): void
    {
        $message = (new AssistantStarterService())->handle(new AssistantStarterContext(
            speakerName: 'Sanyi',
            targetName: 'Mari',
            targetVisibleFacts: [],
            unsafeContext: false,
        ));

        $this->assertSame(
            'Sanyi, Mari mar itt van a matchben, ugyhogy a vilag legegyszerubb nyito kerdese kovetkezik: hogy indult a napod?',
            $message,
        );
    }

    public function test_service_refuses_unsafe_context(): void
    {
        $this->assertNull((new AssistantStarterService())->handle(new AssistantStarterContext(
            speakerName: 'Sanyi',
            targetName: 'Mari',
            targetVisibleFacts: ['medical diagnosis'],
            unsafeContext: true,
        )));
    }
}
