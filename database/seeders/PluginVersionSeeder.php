<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PluginVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'version' => '1.0.0',
                'plugin_id' => 1,
                'user_id' => 1,
                'compatible_zencart_versions' => [1, 2],
            ],
            // Add more versions as needed
        ];
        foreach ($data as $item) {
            $pluginVersion = \App\Models\PluginVersion::create([
                'version' => $item['version'],
                'plugin_id' => $item['plugin_id'],
                'user_id' => $item['user_id'],
            ]);

            if (isset($item['compatible_zencart_versions'])) {
                $pluginVersion->compatibleZenCartVersions()->sync($item['compatible_zencart_versions']);
            }
        }
    }
}
