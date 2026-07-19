<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\ProfileTag;
use App\Models\ProfileTagCategory;
use Database\Seeders\ProfileTagTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProfileTagTaxonomySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_tag_taxonomy_seeder_is_idempotent(): void
    {
        $this->seed(ProfileTagTaxonomySeeder::class);
        $this->seed(ProfileTagTaxonomySeeder::class);

        $this->assertDatabaseHas('profile_tag_categories', [
            'slug' => 'hobbies',
            'label' => 'Hobbies',
        ]);
        $this->assertDatabaseHas('profile_tags', [
            'slug' => 'coffee',
            'label' => 'Coffee',
        ]);
        $this->assertSame(12, ProfileTagCategory::query()->count());
        $this->assertGreaterThanOrEqual(70, ProfileTag::query()->count());
        $this->assertSame(ProfileTag::query()->count(), ProfileTag::query()->distinct('slug')->count('slug'));
    }
}
