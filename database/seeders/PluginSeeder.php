<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pluginData = [
            ['name' => 'Plugin One', 'description' => 'This is the first plugin.', 'status' => 'open'],
            ['name' => 'Plugin Two', 'description' => 'This is the second plugin.', 'status' => 'closed'],
            ['name' => 'Plugin Three', 'description' => 'This is the third plugin.', 'status' => 'locked'],
        ];

        foreach ($pluginData as $entry) {
            \App\Models\Plugin::create($entry);
        }
    }
}
