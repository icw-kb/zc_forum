<?php

namespace Database\Seeders;

use App\Models\ZencartVersion;
use Illuminate\Database\Seeder;

class ZencartVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $versions = [
            '1.5.7',
            '1.5.8',
            '2.0.0',
            '2.0.1',
            '2.1.0',
        ];

        foreach ($versions as $version) {
            ZencartVersion::firstOrCreate(['version' => $version]);
        }
    }
}
