<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ZencartVersion;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ForumGroupSeeder::class,
            ForumSeeder::class,
            ThreadSeeder::class,
            PostSeeder::class,
            ZencartVersionSeeder::class,
            PluginSeeder::class,
        ]);
    }
}
