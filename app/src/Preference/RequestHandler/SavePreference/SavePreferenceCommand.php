<?php

declare(strict_types=1);

namespace App\Preference\RequestHandler\SavePreference;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;

final readonly class SavePreferenceCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    /**
     * @param list<string> $preferredGenders
     * @param list<string> $preferredOrientations
     * @param list<string> $relationshipIntents
     * @param list<string> $dealbreakers
     * @param list<string> $softPreferences
     */
    public function __construct(
        public int $userId,
        public int $preferredAgeMin,
        public int $preferredAgeMax,
        public int $preferredDistanceKm,
        public array $preferredGenders,
        public array $preferredOrientations,
        public array $relationshipIntents,
        public bool $excludeAiWingmanEnabled,
        public array $dealbreakers,
        public array $softPreferences,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            (int) $payload['user_id'],
            (int) $payload['preferred_age_min'],
            (int) $payload['preferred_age_max'],
            (int) $payload['preferred_distance_km'],
            self::stringList($payload['preferred_genders']),
            self::stringList($payload['preferred_orientations']),
            self::stringList($payload['relationship_intents']),
            (bool) ($payload['exclude_ai_wingman_enabled'] ?? false),
            self::stringList($payload['dealbreakers'] ?? []),
            self::stringList($payload['soft_preferences'] ?? []),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'user_id' => 'required',
            'preferred_age_min' => 'required|numeric|between:18,120|lte:preferred_age_max',
            'preferred_age_max' => 'required|numeric|between:18,120',
            'preferred_distance_km' => 'required|numeric|between:1,500',
            'preferred_genders' => 'required|array|min_items:1',
            'preferred_orientations' => 'required|array|min_items:1',
            'relationship_intents' => 'required|array|min_items:1',
            'exclude_ai_wingman_enabled' => 'nullable|boolean',
            'dealbreakers' => 'nullable|array',
            'soft_preferences' => 'nullable|array',
        ];
    }

    /**
     * @return list<string>
     */
    private static function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map(static fn (mixed $item): string => (string) $item, $value));
    }
}
