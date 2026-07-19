<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProfileTag;
use App\Models\ProfileTagCategory;
use Illuminate\Database\Seeder;

final class ProfileTagTaxonomySeeder extends Seeder
{
    /**
     * @var array<string, array{label: string, tags: array<string, string>}>
     */
    private array $taxonomy = [
        'hobbies' => ['label' => 'Hobbies', 'tags' => [
            'photography' => 'Photography', 'cooking' => 'Cooking', 'gardening' => 'Gardening', 'board-games' => 'Board games', 'diy' => 'DIY', 'writing' => 'Writing', 'dancing' => 'Dancing',
        ]],
        'sports-movement' => ['label' => 'Sports and movement', 'tags' => [
            'running' => 'Running', 'gym' => 'Gym', 'yoga' => 'Yoga', 'hiking' => 'Hiking', 'cycling' => 'Cycling', 'swimming' => 'Swimming', 'football' => 'Football', 'basketball' => 'Basketball', 'tennis' => 'Tennis', 'winter-sports' => 'Winter sports',
        ]],
        'music' => ['label' => 'Music', 'tags' => [
            'pop' => 'Pop', 'rock' => 'Rock', 'electronic' => 'Electronic', 'jazz' => 'Jazz', 'classical' => 'Classical', 'concerts' => 'Concerts', 'festivals' => 'Festivals',
        ]],
        'film-tv' => ['label' => 'Film and TV', 'tags' => [
            'sci-fi' => 'Sci-fi', 'fantasy' => 'Fantasy', 'comedy' => 'Comedy', 'thriller' => 'Thriller', 'documentary' => 'Documentary', 'anime' => 'Anime', 'streaming' => 'Streaming',
        ]],
        'food-drink' => ['label' => 'Food and drink', 'tags' => [
            'coffee' => 'Coffee', 'wine' => 'Wine', 'street-food' => 'Street food', 'vegan' => 'Vegan', 'baking' => 'Baking', 'restaurants' => 'Restaurants', 'brunch' => 'Brunch',
        ]],
        'travel' => ['label' => 'Travel', 'tags' => [
            'city-breaks' => 'City breaks', 'beach' => 'Beach', 'road-trips' => 'Road trips', 'nature-trips' => 'Nature trips', 'spontaneous-travel' => 'Spontaneous travel',
        ]],
        'pets' => ['label' => 'Pets', 'tags' => [
            'dogs' => 'Dogs', 'cats' => 'Cats', 'pet-friendly' => 'Pet friendly', 'no-pets' => 'No pets', 'wants-pets' => 'Wants pets',
        ]],
        'lifestyle' => ['label' => 'Lifestyle', 'tags' => [
            'early-bird' => 'Early bird', 'night-owl' => 'Night owl', 'smoke-free' => 'Smoke free', 'rarely-drinks' => 'Rarely drinks', 'active-lifestyle' => 'Active lifestyle',
        ]],
        'values' => ['label' => 'Values', 'tags' => [
            'family-oriented' => 'Family oriented', 'environmental' => 'Environmental', 'ambitious' => 'Ambitious', 'open-minded' => 'Open minded', 'humor-matters' => 'Humor matters',
        ]],
        'culture' => ['label' => 'Culture', 'tags' => [
            'books' => 'Books', 'museums' => 'Museums', 'theatre' => 'Theatre', 'podcasts' => 'Podcasts', 'art-galleries' => 'Art galleries',
        ]],
        'staying-in' => ['label' => 'Staying in', 'tags' => [
            'gaming' => 'Gaming', 'movie-nights' => 'Movie nights', 'home-cooking' => 'Home cooking', 'quiet-weekends' => 'Quiet weekends',
        ]],
        'going-out' => ['label' => 'Going out', 'tags' => [
            'bars' => 'Bars', 'live-events' => 'Live events', 'karaoke' => 'Karaoke', 'social-sports' => 'Social sports',
        ]],
    ];

    public function run(): void
    {
        $categoryOrder = 0;

        foreach ($this->taxonomy as $categorySlug => $categoryData) {
            /** @var ProfileTagCategory $category */
            $category = ProfileTagCategory::query()->updateOrCreate(
                ['slug' => $categorySlug],
                ['label' => $categoryData['label'], 'sort_order' => $categoryOrder++],
            );

            $tagOrder = 0;

            foreach ($categoryData['tags'] as $tagSlug => $tagLabel) {
                ProfileTag::query()->updateOrCreate(
                    ['slug' => $tagSlug],
                    [
                        'profile_tag_category_id' => $category->getKey(),
                        'label' => $tagLabel,
                        'sort_order' => $tagOrder++,
                    ],
                );
            }
        }
    }
}
