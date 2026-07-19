<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DatingProfile;
use App\Models\ProfilePreference;
use App\Models\ProfilePhoto;
use App\Models\ProfileTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DemoDatingProfileSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $cities = ['Budapest', 'Debrecen', 'Szeged', 'Pecs', 'Gyor', 'Miskolc'];

    public function run(): void
    {
        $count = max(0, min(250, (int) config('freedateapp.demo_profile_count', 0)));
        $basePhotoUrl = rtrim((string) config('freedateapp.demo_photo_base_url', 'https://i.pravatar.cc/600'), '?&');

        for ($index = 1; $index <= $count; ++$index) {
            $user = User::query()->updateOrCreate(
                ['email' => sprintf('demo%d@freedateapp.local', $index)],
                [
                    'name' => sprintf('Demo %d', $index),
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'is_admin' => false,
                    'is_active' => true,
                ],
            );

            /** @var DatingProfile $profile */
            $profile = $user->datingProfile()->updateOrCreate([], [
                'display_name' => sprintf('Demo %d', $index),
                'birthdate' => now()->subYears(21 + ($index % 24))->subDays($index)->toDateString(),
                'gender' => $this->cycle(['woman', 'man', 'non_binary'], $index),
                'orientation' => $this->cycle(['straight', 'bisexual', 'other'], $index),
                'city' => $this->cycle($this->cities, $index),
                'country_code' => 'HU',
                'approximate_latitude' => 47.4979 + (($index % 10) / 100),
                'approximate_longitude' => 19.0402 + (($index % 10) / 100),
                'relationship_intent' => $this->cycle(['long_term', 'short_term', 'friendship', 'unsure'], $index),
                'bio' => $this->bioFor($index),
                'visibility_status' => 'visible',
                'moderation_status' => 'approved',
                'ai_wingman_enabled' => 0 === $index % 3,
                'completed_at' => now(),
            ]);

            ProfilePreference::query()->updateOrCreate(
                ['dating_profile_id' => $profile->getKey()],
                [
                    'preferred_age_min' => 18,
                    'preferred_age_max' => 65,
                    'preferred_distance_km' => 100,
                    'preferred_genders' => ['woman', 'man', 'non_binary'],
                    'preferred_orientations' => ['straight', 'bisexual', 'other'],
                    'relationship_intents' => ['long_term', 'short_term', 'friendship', 'unsure'],
                    'exclude_ai_wingman_enabled' => false,
                    'dealbreakers' => [],
                    'soft_preferences' => ['coffee', 'walks', 'music'],
                ],
            );

            ProfilePhoto::query()->updateOrCreate(
                ['dating_profile_id' => $profile->getKey(), 'sort_order' => 0],
                [
                    'path' => $this->photoUrl($basePhotoUrl, $index),
                    'is_primary' => true,
                    'moderation_status' => 'approved',
                ],
            );

            $this->syncTags($profile, $index);
        }
    }

    /**
     * @param list<string> $values
     */
    private function cycle(array $values, int $index): string
    {
        return $values[($index - 1) % count($values)];
    }

    private function bioFor(int $index): string
    {
        $activities = ['coffee', 'city walks', 'live music', 'weekend trips', 'board games', 'quiet dinners'];

        return sprintf('I like %s and honest conversations. Demo profile %d for local discovery testing.', $this->cycle($activities, $index), $index);
    }

    private function photoUrl(string $basePhotoUrl, int $index): string
    {
        $separator = str_contains($basePhotoUrl, '?') ? '&' : '?';
        $imageId = (($index - 1) % 70) + 1;

        return sprintf('%s%simg=%d', $basePhotoUrl, $separator, $imageId);
    }

    private function syncTags(DatingProfile $profile, int $index): void
    {
        $tagSlugs = [
            $this->cycle(['coffee', 'hiking', 'concerts', 'dogs', 'books'], $index),
            $this->cycle(['cooking', 'running', 'comedy', 'cats', 'gaming'], $index + 1),
            $this->cycle(['city-breaks', 'road-trips', 'movie-nights', 'open-minded', 'brunch'], $index + 2),
            $this->cycle(['yoga', 'jazz', 'documentary', 'pet-friendly', 'humor-matters'], $index + 3),
        ];

        $tags = ProfileTag::query()->whereIn('slug', $tagSlugs)->get();
        $syncPayload = [];
        $sortOrder = 0;

        foreach ($tags as $tag) {
            $syncPayload[$tag->getKey()] = [
                'visibility' => 2 === $sortOrder ? 'matches_only' : (3 === $sortOrder ? 'private_matching' : 'public'),
                'sort_order' => $sortOrder++,
            ];
        }

        $profile->tags()->sync($syncPayload);
    }
}
