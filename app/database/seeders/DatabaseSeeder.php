<?php

namespace Database\Seeders;

use App\Models\User;
use App\User\Enum\AccountStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProfileTagTaxonomySeeder::class);

        $admin = User::query()->firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ],
        );

        $admin->forceFill([
            'role' => 'admin',
            'is_admin' => true,
            'is_active' => true,
            'account_status' => AccountStatus::ACTIVE,
            'email_verified_at' => now(),
        ])->save();

        User::query()->firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Teszt Vasarlo',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_admin' => false,
                'is_active' => true,
                'account_status' => AccountStatus::ACTIVE,
                'email_verified_at' => now(),
            ],
        );

        $this->call(DemoDatingProfileSeeder::class);
        $this->call(MtamDemoScenarioSeeder::class);
    }
}
