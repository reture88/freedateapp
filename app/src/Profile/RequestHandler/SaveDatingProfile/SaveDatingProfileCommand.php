<?php

declare(strict_types=1);

namespace App\Profile\RequestHandler\SaveDatingProfile;

use App\Shared\DomainService\Bus\RequestHandler\SelfConstructingRequestInterface;
use App\Shared\DomainService\Bus\RequestHandler\ValidateRequestInterface;
use App\Shared\Infrastructure\Bus\Command\CommandInterface;
use Carbon\CarbonImmutable;

final readonly class SaveDatingProfileCommand implements CommandInterface, SelfConstructingRequestInterface, ValidateRequestInterface
{
    public function __construct(
        public int $userId,
        public string $displayName,
        public string $birthdate,
        public string $gender,
        public string $orientation,
        public string $city,
        public string $countryCode,
        public ?float $approximateLatitude,
        public ?float $approximateLongitude,
        public string $relationshipIntent,
        public ?string $bio,
        public bool $aiWingmanEnabled,
        /** @var array<int, string> */
        public array $profileTags,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function createFromArray(array $payload): static
    {
        return new self(
            (int) $payload['user_id'],
            (string) $payload['display_name'],
            CarbonImmutable::parse((string) $payload['birthdate'])->toDateString(),
            (string) $payload['gender'],
            (string) $payload['orientation'],
            (string) $payload['city'],
            strtoupper((string) $payload['country_code']),
            isset($payload['approximate_latitude']) && '' !== $payload['approximate_latitude'] ? (float) $payload['approximate_latitude'] : null,
            isset($payload['approximate_longitude']) && '' !== $payload['approximate_longitude'] ? (float) $payload['approximate_longitude'] : null,
            (string) $payload['relationship_intent'],
            isset($payload['bio']) && '' !== $payload['bio'] ? (string) $payload['bio'] : null,
            (bool) ($payload['ai_wingman_enabled'] ?? false),
            self::profileTagsFromPayload($payload),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function rules(): array
    {
        return [
            'user_id' => 'required',
            'display_name' => 'required|max:80',
            'birthdate' => 'required|date|adult',
            'gender' => 'required|in:woman,man,non_binary,other',
            'orientation' => 'required|in:straight,gay,lesbian,bisexual,pansexual,queer,other',
            'city' => 'required|max:120',
            'country_code' => 'required|size:2',
            'approximate_latitude' => 'nullable|numeric|between:-90,90',
            'approximate_longitude' => 'nullable|numeric|between:-180,180',
            'relationship_intent' => 'required|in:long_term,short_term,friendship,unsure',
            'bio' => 'nullable|max:1000',
            'ai_wingman_enabled' => 'nullable|boolean',
            'privacy_consent' => 'required|accepted',
        ];
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<int, string>
     */
    private static function profileTagsFromPayload(array $payload): array
    {
        $normalized = [];
        $allowedVisibilities = ['public', 'matches_only', 'private_matching'];
        $profileTags = $payload['profile_tags'] ?? [];

        if (is_array($profileTags)) {
            foreach ($profileTags as $tagId => $visibility) {
                if (!in_array($visibility, $allowedVisibilities, true)) {
                    continue;
                }

                $normalized[(int) $tagId] = (string) $visibility;
            }
        }

        $selectedTags = $payload['selected_profile_tags'] ?? [];
        $visibilityMap = $payload['profile_tag_visibility'] ?? [];

        if (is_array($selectedTags) && is_array($visibilityMap)) {
            foreach ($selectedTags as $tagId) {
                $visibility = $visibilityMap[(string) $tagId] ?? 'public';

                if (!in_array($visibility, $allowedVisibilities, true)) {
                    $visibility = 'public';
                }

                $normalized[(int) $tagId] = (string) $visibility;
            }
        }

        return array_slice($normalized, 0, 20, true);
    }
}
