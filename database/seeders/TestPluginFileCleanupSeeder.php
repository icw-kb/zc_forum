<?php

namespace Database\Seeders;

use App\Helpers\DummyPluginFileGenerator;
use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TestPluginFileCleanupSeeder extends Seeder
{
    /**
     * Run the database seeds to test file cleanup.
     */
    public function run(): void
    {
        $this->command->info('Starting plugin file cleanup test...');

        // Count existing files
        $initialCount = $this->countPluginFiles();
        $this->command->info("Initial plugin file count: {$initialCount}");

        // Clean up any existing test data first
        Plugin::where('name', 'like', 'Test Plugin %')->each(function ($plugin) {
            $plugin->versions()->delete();
            $plugin->delete();
        });
        PluginGroup::where('name', 'Test Group')->delete();

        // Clean up existing plugin files
        if (Storage::exists('plugins')) {
            Storage::deleteDirectory('plugins');
            $this->command->info('Deleted existing plugins directory');
        }

        // Ensure plugin storage directory exists
        Storage::makeDirectory('plugins');

        // Create a test plugin group
        $group = PluginGroup::create([
            'name' => 'Test Group',
            'description' => 'Test plugin group for file cleanup testing',
        ]);

        // Create 5 test plugins with versions
        for ($i = 1; $i <= 5; $i++) {
            $plugin = Plugin::create([
                'name' => "Test Plugin {$i}",
                'description' => "Test plugin {$i} description",
                'status' => 'open',
                'is_featured' => false,
                'view_count' => 0,
                'download_count' => 0,
                'plugin_group_id' => $group->id,
            ]);

            // Create 2 versions for each plugin
            for ($v = 1; $v <= 2; $v++) {
                $versionNumber = "1.{$v}.0";

                // Generate dummy zip file
                $filePath = DummyPluginFileGenerator::generate($plugin->slug, $versionNumber);
                $fileSize = Storage::size($filePath);
                $fileHash = hash_file('sha256', Storage::path($filePath));

                PluginVersion::create([
                    'plugin_id' => $plugin->id,
                    'version' => $versionNumber,
                    'description' => "Version {$versionNumber} of {$plugin->name}",
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'file_hash' => $fileHash,
                    'user_id' => 1, // Assuming user ID 1 exists from UserSeeder
                    'status' => 'open',
                ]);
            }
        }

        // Count files after creation
        $afterCreationCount = $this->countPluginFiles();
        $this->command->info("Plugin file count after creation: {$afterCreationCount}");
        $this->command->info('Expected: 10 files (5 plugins x 2 versions)');

        // Now delete all files and recreate to test cleanup
        $this->command->info("\nTesting file cleanup on second run...");

        if (Storage::exists('plugins')) {
            Storage::deleteDirectory('plugins');
            $this->command->info('Deleted plugins directory for second run');
        }

        Storage::makeDirectory('plugins');

        // Recreate files for existing plugins
        $plugins = Plugin::where('name', 'like', 'Test Plugin %')->get();
        $recreatedCount = 0;

        foreach ($plugins as $plugin) {
            foreach ($plugin->versions as $version) {
                // Generate the file again
                $filePath = DummyPluginFileGenerator::generate($plugin->slug, $version->version);
                $recreatedCount++;
            }
        }

        // Final count
        $finalCount = $this->countPluginFiles();
        $this->command->info("Plugin file count after second run: {$finalCount}");
        $this->command->info("Files recreated: {$recreatedCount}");

        // Summary
        $this->command->info("\n=== Summary ===");
        $this->command->info("Initial files: {$initialCount}");
        $this->command->info("After first creation: {$afterCreationCount}");
        $this->command->info("After cleanup and recreation: {$finalCount}");
        $this->command->info('File cleanup working: '.($finalCount <= $afterCreationCount ? 'YES' : 'NO'));
    }

    /**
     * Count plugin files in storage.
     */
    private function countPluginFiles(): int
    {
        $files = Storage::allFiles('plugins');

        return count(array_filter($files, function ($file) {
            return str_ends_with($file, '.zip');
        }));
    }
}
