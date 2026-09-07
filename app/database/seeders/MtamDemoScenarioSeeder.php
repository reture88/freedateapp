<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Connection;
use App\Models\Conversation;
use App\Models\DatingProfile;
use App\Models\DiscoveryDecision;
use App\Models\Message;
use App\Models\ProfilePreference;
use App\Models\ProfilePhoto;
use App\Models\ProfileTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

final class MtamDemoScenarioSeeder extends Seeder
{
    private const DEMO_EMAIL = 'mtam.demo@freedateapp.local';

    public function run(): void
    {
        $viewer = $this->profile(
            self::DEMO_EMAIL,
            'MTAM Demo',
            [
                'birthdate' => '1993-04-12',
                'gender' => 'woman',
                'orientation' => 'bisexual',
                'city' => 'Budapest',
                'relationship_intent' => 'long_term',
                'bio' => 'Coffee after work, weekend hikes, galleries and conversations that run late in a good way.',
                'ai_wingman_enabled' => true,
                'photo_index' => 31,
                'tags' => ['coffee', 'hiking', 'jazz', 'city-breaks', 'books', 'open-minded'],
            ],
        );

        $this->preference($viewer);

        $matches = [
            ['email' => 'mtam.match.nora@freedateapp.local', 'name' => 'Nora', 'birthdate' => '1996-02-17', 'city' => 'Budapest', 'intent' => 'long_term', 'photo' => 44, 'matched_at' => now()->subDay(), 'unread' => ['I found a tiny jazz place for Thursday.']],
            ['email' => 'mtam.match.lili@freedateapp.local', 'name' => 'Lili', 'birthdate' => '1992-09-03', 'city' => 'Szeged', 'intent' => 'friendship', 'photo' => 47, 'matched_at' => now()->subDays(2), 'unread' => []],
            ['email' => 'mtam.match.eszter@freedateapp.local', 'name' => 'Eszter', 'birthdate' => '1994-11-26', 'city' => 'Debrecen', 'intent' => 'long_term', 'photo' => 51, 'matched_at' => now()->subDays(12), 'unread' => ['Your hiking photo is great.', 'Do you prefer morning trails?']],
            ['email' => 'mtam.match.reka@freedateapp.local', 'name' => 'Reka', 'birthdate' => '1998-07-08', 'city' => 'Gyor', 'intent' => 'unsure', 'photo' => 55, 'matched_at' => now()->subDays(26), 'unread' => []],
            ['email' => 'mtam.match.zsofi@freedateapp.local', 'name' => 'Zsofi', 'birthdate' => '1991-01-30', 'city' => 'Pecs', 'intent' => 'short_term', 'photo' => 59, 'matched_at' => now()->subDays(41), 'unread' => []],
        ];

        foreach ($matches as $index => $match) {
            $profile = $this->profile($match['email'], $match['name'], [
                'birthdate' => $match['birthdate'],
                'gender' => 1 === $index % 2 ? 'non_binary' : 'woman',
                'orientation' => 2 === $index % 3 ? 'other' : 'bisexual',
                'city' => $match['city'],
                'relationship_intent' => $match['intent'],
                'bio' => sprintf('%s likes easy plans, thoughtful dates and making space for real conversation.', $match['name']),
                'ai_wingman_enabled' => 0 === $index % 2,
                'photo_index' => $match['photo'],
                'tags' => ['coffee', 'city-breaks', 'books', 'restaurants'],
            ]);

            $this->preference($profile);
            $conversation = $this->match($viewer, $profile, Carbon::parse($match['matched_at']));
            $this->messages($conversation, $profile->user, $match['unread']);
        }

        $candidates = [
            ['email' => 'mtam.candidate.anna@freedateapp.local', 'name' => 'Anna', 'birthdate' => '1997-06-21', 'gender' => 'woman', 'city' => 'Budapest', 'intent' => 'long_term', 'photo' => 62, 'tags' => ['coffee', 'hiking', 'cats', 'city-breaks', 'open-minded'], 'bio' => 'Weekend trips, good coffee, long conversations and someone curious about the world.'],
            ['email' => 'mtam.candidate.mira@freedateapp.local', 'name' => 'Mira', 'birthdate' => '1995-12-09', 'gender' => 'woman', 'city' => 'Szentendre', 'intent' => 'friendship', 'photo' => 64, 'tags' => ['jazz', 'books', 'museums', 'brunch'], 'bio' => 'Museum afternoons, spontaneous brunch and calm people who ask better questions.'],
            ['email' => 'mtam.candidate.dani@freedateapp.local', 'name' => 'Dani', 'birthdate' => '1990-08-15', 'gender' => 'non_binary', 'city' => 'Budapest', 'intent' => 'unsure', 'photo' => 66, 'tags' => ['running', 'coffee', 'documentary', 'humor-matters'], 'bio' => 'Training for a half marathon, making strong coffee and collecting documentary recommendations.'],
            ['email' => 'mtam.candidate.sara@freedateapp.local', 'name' => 'Sara', 'birthdate' => '1999-03-02', 'gender' => 'woman', 'city' => 'Eger', 'intent' => 'long_term', 'photo' => 68, 'tags' => ['wine', 'road-trips', 'books', 'open-minded'], 'bio' => 'Road trips, small wineries and someone who can make ordinary weekdays feel warmer.'],
            ['email' => 'mtam.candidate.mark@freedateapp.local', 'name' => 'Mark', 'birthdate' => '1989-10-19', 'gender' => 'man', 'city' => 'Budapest', 'intent' => 'long_term', 'photo' => 70, 'tags' => ['hiking', 'cooking', 'jazz', 'family-oriented'], 'bio' => 'Home cooking, early hikes and a soft spot for tiny jazz bars.'],
        ];

        foreach ($candidates as $candidate) {
            $profile = $this->profile($candidate['email'], $candidate['name'], [
                'birthdate' => $candidate['birthdate'],
                'gender' => $candidate['gender'],
                'orientation' => 'bisexual',
                'city' => $candidate['city'],
                'relationship_intent' => $candidate['intent'],
                'bio' => $candidate['bio'],
                'ai_wingman_enabled' => true,
                'photo_index' => $candidate['photo'],
                'tags' => $candidate['tags'],
            ]);

            $this->preference($profile);
        }
    }

    /**
     * @param array{
     *     birthdate: string,
     *     gender: string,
     *     orientation: string,
     *     city: string,
     *     relationship_intent: string,
     *     bio: string,
     *     ai_wingman_enabled: bool,
     *     photo_index: int,
     *     tags: list<string>
     * } $data
     */
    private function profile(string $email, string $displayName, array $data): DatingProfile
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $displayName,
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_admin' => false,
                'is_active' => true,
            ],
        );

        /** @var DatingProfile $profile */
        $profile = $user->datingProfile()->updateOrCreate([], [
            'display_name' => $displayName,
            'birthdate' => $data['birthdate'],
            'gender' => $data['gender'],
            'orientation' => $data['orientation'],
            'city' => $data['city'],
            'country_code' => 'HU',
            'approximate_latitude' => 47.4979,
            'approximate_longitude' => 19.0402,
            'relationship_intent' => $data['relationship_intent'],
            'bio' => $data['bio'],
            'visibility_status' => 'visible',
            'moderation_status' => 'approved',
            'ai_wingman_enabled' => $data['ai_wingman_enabled'],
            'completed_at' => now(),
        ]);

        ProfilePhoto::query()->updateOrCreate(
            ['dating_profile_id' => $profile->getKey(), 'sort_order' => 0],
            [
                'path' => sprintf('https://i.pravatar.cc/600?img=%d', $data['photo_index']),
                'is_primary' => true,
                'moderation_status' => 'approved',
            ],
        );

        $tags = ProfileTag::query()->whereIn('slug', $data['tags'])->get();
        $syncPayload = [];

        foreach ($tags as $index => $tag) {
            $syncPayload[$tag->getKey()] = [
                'visibility' => $index < 4 ? 'public' : 'matches_only',
                'sort_order' => $index,
            ];
        }

        $profile->tags()->sync($syncPayload);

        return $profile;
    }

    private function preference(DatingProfile $profile): void
    {
        ProfilePreference::query()->updateOrCreate(
            ['dating_profile_id' => $profile->getKey()],
            [
                'preferred_age_min' => 24,
                'preferred_age_max' => 42,
                'preferred_distance_km' => 120,
                'preferred_genders' => ['woman', 'man', 'non_binary'],
                'preferred_orientations' => ['straight', 'bisexual', 'other'],
                'relationship_intents' => ['long_term', 'short_term', 'friendship', 'unsure'],
                'exclude_ai_wingman_enabled' => false,
                'dealbreakers' => [],
                'soft_preferences' => ['coffee', 'hiking', 'books', 'jazz'],
            ],
        );
    }

    private function match(DatingProfile $viewer, DatingProfile $other, Carbon $matchedAt): Conversation
    {
        $profileIds = [(int) $viewer->getKey(), (int) $other->getKey()];
        sort($profileIds);

        $connection = Connection::query()->updateOrCreate([
            'profile_one_id' => $profileIds[0],
            'profile_two_id' => $profileIds[1],
        ], [
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => $matchedAt,
        ]);

        $connection->forceFill([
            'profile_one_decision' => 'accepted',
            'profile_two_decision' => 'accepted',
            'status' => 'matched',
            'matched_at' => $matchedAt,
            'unmatched_at' => null,
            'blocked_at' => null,
        ])->save();

        DiscoveryDecision::query()->updateOrCreate(
            [
                'viewer_profile_id' => $viewer->getKey(),
                'target_profile_id' => $other->getKey(),
            ],
            [
                'decision' => 'accepted',
                'decided_at' => $matchedAt,
            ],
        );

        DiscoveryDecision::query()->updateOrCreate(
            [
                'viewer_profile_id' => $other->getKey(),
                'target_profile_id' => $viewer->getKey(),
            ],
            [
                'decision' => 'accepted',
                'decided_at' => $matchedAt,
            ],
        );

        return Conversation::query()->firstOrCreate(['connection_id' => $connection->getKey()]);
    }

    /**
     * @param list<string> $unreadMessages
     */
    private function messages(Conversation $conversation, User $sender, array $unreadMessages): void
    {
        Message::query()->whereBelongsTo($conversation)->delete();

        Message::query()->create([
            'conversation_id' => $conversation->getKey(),
            'sender_user_id' => null,
            'sender_type' => 'assistant',
            'body' => 'A light opener: ask about the small thing that made their week better.',
            'read_at' => now()->subDay(),
        ]);

        foreach ($unreadMessages as $message) {
            Message::query()->create([
                'conversation_id' => $conversation->getKey(),
                'sender_user_id' => $sender->getKey(),
                'sender_type' => 'user',
                'body' => $message,
                'read_at' => null,
            ]);
        }
    }
}
