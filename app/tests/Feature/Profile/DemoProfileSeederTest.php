<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\DatingProfile;
use App\Models\ProfilePhoto;
use Database\Seeders\DemoDatingProfileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

final class DemoProfileSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_profile_seeder_uses_configured_count_and_external_photo_urls(): void
    {
        Config::set('freedateapp.demo_profile_count', 3);
        Config::set('freedateapp.demo_photo_base_url', 'https://i.pravatar.cc/600');

        $this->seed(DemoDatingProfileSeeder::class);

        $this->assertSame(3, DatingProfile::query()->where('display_name', 'like', 'Demo %')->count());
        $this->assertSame(3, ProfilePhoto::query()->where('path', 'like', 'https://i.pravatar.cc/600%')->count());
        $this->assertSame(3, ProfilePhoto::query()->where('is_primary', true)->count());
    }
}
