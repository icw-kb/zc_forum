<?php

namespace Database\Seeders;

use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginStatistic;
use App\Models\PluginVersion;
use App\Models\User;
use App\Models\ZencartVersion;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create plugin groups first
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
            $pluginGroups[] = PluginGroup::create($groupData);
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
                'github_url' => 'https://github.com/example/paypal-express',
                'status' => 'open',
                'featured' => true,
                'view_count' => 1250,
                'download_count' => 420,
                'plugin_group_id' => $pluginGroups[0]->id, // Payment Modules
            ],
            [
                'name' => 'Advanced Search',
                'description' => 'Enhanced search functionality with filters and autocomplete.',
                'github_url' => 'https://github.com/example/advanced-search',
                'status' => 'open',
                'featured' => false,
                'view_count' => 850,
                'download_count' => 320,
                'plugin_group_id' => $pluginGroups[3]->id, // Customer Features
            ],
            [
                'name' => 'UPS Shipping Calculator',
                'description' => 'Real-time UPS shipping rate calculation and tracking.',
                'github_url' => 'https://github.com/example/ups-shipping',
                'status' => 'open',
                'featured' => true,
                'view_count' => 920,
                'download_count' => 180,
                'plugin_group_id' => $pluginGroups[1]->id, // Shipping Modules
            ],
            [
                'name' => 'SEO URL Manager',
                'description' => 'Generate SEO-friendly URLs and manage redirects.',
                'github_url' => null,
                'status' => 'open',
                'featured' => false,
                'view_count' => 650,
                'download_count' => 240,
                'plugin_group_id' => $pluginGroups[4]->id, // SEO Tools
            ],
            [
                'name' => 'Deprecated Plugin',
                'description' => 'This plugin is no longer maintained.',
                'github_url' => null,
                'status' => 'closed',
                'featured' => false,
                'view_count' => 120,
                'download_count' => 15,
                'plugin_group_id' => $pluginGroups[2]->id, // Admin Tools
            ],
        ];

        foreach ($testPlugins as $pluginData) {
            $plugin = Plugin::create($pluginData);

            // Create 1-3 versions for each plugin
            $versionCount = rand(1, 3);
            for ($i = 0; $i < $versionCount; $i++) {
                $version = PluginVersion::factory()
                    ->for($plugin)
                    ->create([
                        'version' => ($i === 0) ? '1.'.rand(0, 5).'.'.rand(0, 9) :
                                   (($i === 1) ? '1.'.rand(6, 9).'.'.rand(0, 9) :
                                    '2.'.rand(0, 2).'.'.rand(0, 9)),
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
        foreach ($pluginGroups as $group) {
            $pluginCount = rand(2, 5);
            $plugins = Plugin::factory()
                ->count($pluginCount)
                ->for($group, 'group')
                ->active()
                ->create();

            foreach ($plugins as $plugin) {
                // Create 1-2 versions
                $versionCount = rand(1, 2);
                for ($i = 0; $i < $versionCount; $i++) {
                    $version = PluginVersion::factory()
                        ->for($plugin)
                        ->create();

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
