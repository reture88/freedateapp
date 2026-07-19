<?php

declare(strict_types=1);

namespace App\Discovery\DomainService;

use App\Discovery\Dto\DistanceCalculator;
use App\Models\Block;
use App\Models\DatingProfile;
use App\Models\DiscoveryDecision;
use App\Models\ProfilePreference;
use Carbon\CarbonImmutable;

final class FindNextCandidateService
{
    public function __construct(private ?DistanceCalculator $distanceCalculator = null)
    {
    }

    public function handle(DatingProfile $viewerProfile): ?DatingProfile
    {
        $preference = $viewerProfile->preference()->first();

        if (!$preference instanceof ProfilePreference) {
            return null;
        }

        $excludedProfileIds = DiscoveryDecision::query()
            ->where('viewer_profile_id', $viewerProfile->getKey())
            ->pluck('target_profile_id')
            ->all();

        $blockedUserIds = $this->blockedUserIds((int) $viewerProfile->user_id);

        /** @var iterable<DatingProfile> $candidates */
        $candidates = DatingProfile::query()
            ->whereKeyNot($viewerProfile->getKey())
            ->whereNotIn('id', $excludedProfileIds)
            ->whereNotIn('user_id', $blockedUserIds)
            ->whereNotNull('completed_at')
            ->where('visibility_status', 'visible')
            ->where('moderation_status', 'approved')
            ->whereIn('gender', $preference->preferred_genders)
            ->whereIn('orientation', $preference->preferred_orientations)
            ->whereIn('relationship_intent', $preference->relationship_intents)
            ->when($preference->exclude_ai_wingman_enabled, static function ($query): void {
                $query->where('ai_wingman_enabled', false);
            })
            ->orderBy('id')
            ->get();

        foreach ($candidates as $candidate) {
            if (!$this->passesAgeFilter($candidate, $preference)) {
                continue;
            }

            if (!$this->passesDistanceFilter($viewerProfile, $candidate, $preference)) {
                continue;
            }

            return $candidate;
        }

        return null;
    }

    /**
     * @return list<int>
     */
    private function blockedUserIds(int $viewerUserId): array
    {
        $blocked = Block::query()
            ->where('blocker_user_id', $viewerUserId)
            ->pluck('blocked_user_id')
            ->all();
        $blockedBy = Block::query()
            ->where('blocked_user_id', $viewerUserId)
            ->pluck('blocker_user_id')
            ->all();

        return array_values(array_unique(array_map('intval', array_merge($blocked, $blockedBy))));
    }

    private function passesAgeFilter(DatingProfile $candidate, ProfilePreference $preference): bool
    {
        $age = CarbonImmutable::parse((string) $candidate->birthdate)->age;

        return $age >= $preference->preferred_age_min && $age <= $preference->preferred_age_max;
    }

    private function passesDistanceFilter(DatingProfile $viewer, DatingProfile $candidate, ProfilePreference $preference): bool
    {
        if (null === $viewer->approximate_latitude || null === $viewer->approximate_longitude) {
            return true;
        }

        if (null === $candidate->approximate_latitude || null === $candidate->approximate_longitude) {
            return false;
        }

        $calculator = $this->distanceCalculator ?? new DistanceCalculator();

        return $calculator->kilometers(
            $viewer->approximate_latitude,
            $viewer->approximate_longitude,
            $candidate->approximate_latitude,
            $candidate->approximate_longitude,
        ) <= $preference->preferred_distance_km;
    }
}
