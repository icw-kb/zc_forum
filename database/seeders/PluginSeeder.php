<?php

namespace Database\Seeders;

use App\Helpers\DummyPluginFileGenerator;
use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginStatistic;
use App\Models\PluginVersion;
use App\Models\User;
use App\Models\ZencartVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PluginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean up existing plugin files
        if (Storage::exists('plugins')) {
            Storage::deleteDirectory('plugins');
        }

        // Ensure plugin storage directory exists
        Storage::makeDirectory('plugins');

        // Create plugin groups first (using firstOrCreate to handle duplicates)
        $groups = [
            ['name' => 'Payment Modules', 'description' => 'Payment processing plugins for e-commerce'],
            ['name' => 'Shipping Modules', 'description' => 'Shipping calculation and integration plugins'],
            ['name' => 'Admin Tools', 'description' => 'Administrative and management tools'],
            ['name' => 'Customer Features', 'description' => 'Customer-facing functionality enhancements'],
            ['name' => 'SEO Tools', 'description' => 'Search engine optimization plugins'],
            ['name' => 'Security', 'description' => 'Security and authentication enhancements'],
        ];

        $pluginGroups = [];
        foreach ($groups as $groupData) {
            $pluginGroups[] = PluginGroup::firstOrCreate(
                ['name' => $groupData['name']],
                $groupData
            );
        }

        // Get some users for statistics (if any exist)
        $users = User::limit(10)->pluck('id')->toArray();

        // Get ZenCart versions for compatibility (if any exist)
        $zcVersions = ZencartVersion::limit(5)->pluck('id')->toArray();

        // Create specific test plugins
        $testPlugins = [
            [
                'name' => 'PayPal Express Checkout',
                'description' => 'Integrate PayPal Express Checkout into your store for faster customer payments.',
                'status' => 'open',
                'is_featured' => true,
                'view_count' => 1250,
                'download_count' => 420,
                'plugin_group_id' => $pluginGroups[0]->id, // Payment Modules
                'vc_url' => 'https://github.com/example/paypal-express',
            ],
            [
                'name' => 'Advanced Search',
                'description' => 'Enhanced search functionality with filters and autocomplete.',
                'status' => 'open',
                'is_featured' => false,
                'view_count' => 850,
                'download_count' => 320,
                'plugin_group_id' => $pluginGroups[3]->id, // Customer Features
                'vc_url' => 'https://github.com/example/advanced-search',
            ],
            [
                'name' => 'UPS Shipping Calculator',
                'description' => 'Real-time UPS shipping rate calculation and tracking.',
                'status' => 'open',
                'is_featured' => true,
                'view_count' => 920,
                'download_count' => 180,
                'plugin_group_id' => $pluginGroups[1]->id, // Shipping Modules
                'vc_url' => 'https://github.com/example/ups-shipping',
            ],
            [
                'name' => 'SEO URL Manager',
                'description' => 'Generate SEO-friendly URLs and manage redirects.',
                'status' => 'open',
                'is_featured' => false,
                'view_count' => 650,
                'download_count' => 240,
                'plugin_group_id' => $pluginGroups[4]->id, // SEO Tools
                'vc_url' => null,
            ],
            [
                'name' => 'Deprecated Plugin',
                'description' => 'This plugin is no longer maintained.',
                'status' => 'closed',
                'is_featured' => false,
                'view_count' => 120,
                'download_count' => 15,
                'plugin_group_id' => $pluginGroups[2]->id, // Admin Tools
                'vc_url' => null,
            ],
        ];

        foreach ($testPlugins as $pluginData) {
            // Extract vc_url for versions
            $vcUrl = $pluginData['vc_url'] ?? null;
            unset($pluginData['vc_url']);
            
            $plugin = Plugin::create($pluginData);

            // Create 2-15 versions for each plugin
            $versionCount = rand(2, 15);
            for ($i = 0; $i < $versionCount; $i++) {
                // Generate progressive version numbers
                $major = intval($i / 5) + 1;
                $minor = ($i % 5) + rand(0, 4);
                $patch = rand(0, 9);
                $versionNumber = "$major.$minor.$patch";

                // Generate dummy zip file
                $filePath = DummyPluginFileGenerator::generate($plugin->slug, $versionNumber);
                $fileSize = Storage::size($filePath);
                $fileHash = hash_file('sha256', Storage::path($filePath));

                $version = PluginVersion::factory()
                    ->for($plugin)
                    ->create([
                        'version' => $versionNumber,
                        'file_path' => $filePath,
                        'file_size' => $fileSize,
                        'file_hash' => $fileHash,
                        'vc_url' => $vcUrl,
                    ]);

                // Attach ZenCart versions if they exist
                if (! empty($zcVersions)) {
                    $version->zencartVersions()->attach(
                        collect($zcVersions)->random(rand(1, min(3, count($zcVersions))))
                    );
                }
            }

            // Create some statistics for popular plugins
            if ($plugin->view_count > 500) {
                // Create view statistics
                $viewCount = rand(20, 50);
                for ($i = 0; $i < $viewCount; $i++) {
                    PluginStatistic::factory()
                        ->for($plugin)
                        ->when(! empty($users) && rand(0, 1), function ($factory) use ($users) {
                            return $factory->for(User::find(collect($users)->random()));
                        })
                        ->view()
                        ->create([
                            'created_at' => now()->subDays(rand(1, 30)),
                        ]);
                }

                // Create download statistics
                $downloadCount = rand(10, 30);
                for ($i = 0; $i < $downloadCount; $i++) {
                    PluginStatistic::factory()
                        ->for($plugin)
                        ->when(! empty($users) && rand(0, 1), function ($factory) use ($users) {
                            return $factory->for(User::find(collect($users)->random()));
                        })
                        ->download()
                        ->create([
                            'created_at' => now()->subDays(rand(1, 30)),
                        ]);
                }
            }
        }

        // Create additional random plugins for bulk testing
        // Ensure we have at least 30 plugins total
        $remainingPlugins = max(0, 30 - count($testPlugins));
        $pluginsPerGroup = intval($remainingPlugins / count($pluginGroups)) + 1;
        
        foreach ($pluginGroups as $group) {
            $pluginCount = $pluginsPerGroup;
            $plugins = Plugin::factory()
                ->count($pluginCount)
                ->for($group, 'group')
                ->active()
                ->create();

            foreach ($plugins as $plugin) {
                // Create 2-15 versions
                $versionCount = rand(2, 15);
                for ($i = 0; $i < $versionCount; $i++) {
                    // Generate progressive version numbers
                    $major = intval($i / 5) + 1;
                    $minor = ($i % 5) + rand(0, 4);
                    $patch = rand(0, 9);
                    $versionNumber = "$major.$minor.$patch";

                    // Generate dummy zip file
                    $filePath = DummyPluginFileGenerator::generate($plugin->slug, $versionNumber);
                    $fileSize = Storage::size($filePath);
                    $fileHash = hash_file('sha256', Storage::path($filePath));

                    $version = PluginVersion::factory()
                        ->for($plugin)
                        ->create([
                            'version' => $versionNumber,
                            'file_path' => $filePath,
                            'file_size' => $fileSize,
                            'file_hash' => $fileHash,
                        ]);

                    // Attach random ZenCart versions if they exist
                    if (! empty($zcVersions)) {
                        $version->zencartVersions()->attach(
                            collect($zcVersions)->random(rand(1, min(2, count($zcVersions))))
                        );
                    }
                }

                // Create some random statistics
                if (rand(0, 1)) {
                    $statCount = rand(5, 15);
                    for ($i = 0; $i < $statCount; $i++) {
                        PluginStatistic::factory()
                            ->for($plugin)
                            ->when(! empty($users) && rand(0, 1), function ($factory) use ($users) {
                                return $factory->for(User::find(collect($users)->random()));
                            })
                            ->create([
                                'created_at' => now()->subDays(rand(1, 60)),
                            ]);
                    }
                }
            }
        }
    }
}
