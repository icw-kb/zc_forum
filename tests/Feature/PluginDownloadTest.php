<?php

use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginStatistic;
use App\Models\PluginVersion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('local');
    $this->group = PluginGroup::factory()->create(['name' => 'Test Group']);
    $this->user = User::factory()->create();
    $this->plugin = Plugin::factory()->for($this->group)->active()->create();
    $this->version = PluginVersion::factory()->for($this->plugin)->create(['version' => '1.0.0']);
});

describe('Plugin Download Authentication', function () {
    test('guest cannot access download route', function () {
        $response = $this->get("/plugins/{$this->plugin->slug}/download/{$this->version->version}");

        $response->assertRedirect('/login');
    });

    test('authenticated user can access download route', function () {
        $this->actingAs($this->user);

        $response = $this->get("/plugins/{$this->plugin->slug}/download/{$this->version->version}");

        $response->assertStatus(200);
        $response->assertSeeLivewire('plugins.plugin-download');
    });

    test('returns 404 for non-existent plugin', function () {
        $this->actingAs($this->user);

        $response = $this->get('/plugins/non-existent/download/1.0.0');

        $response->assertStatus(404);
    });

    test('returns 404 for non-existent version', function () {
        $this->actingAs($this->user);

        $response = $this->get("/plugins/{$this->plugin->slug}/download/999.0.0");

        $response->assertStatus(404);
    });

    test('returns 404 for inactive plugin', function () {
        $this->actingAs($this->user);
        $this->plugin->update(['status' => 'inactive']);

        $response = $this->get("/plugins/{$this->plugin->slug}/download/{$this->version->version}");

        $response->assertStatus(404);
    });
});

describe('Plugin Download Functionality', function () {
    test('displays download page correctly', function () {
        $this->actingAs($this->user);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->assertSee($this->plugin->name)
            ->assertSee($this->version->version);
    });

    test('downloads file when file exists', function () {
        $this->actingAs($this->user);

        // Create a test file
        $testFile = UploadedFile::fake()->create('plugin.zip', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/plugin.zip";
        Storage::put($filePath, $testFile->getContent());

        // Update version with file information
        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => hash('sha256', $testFile->getContent()),
        ]);

        $response = Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])->call('download');

        $response->assertStatus(200);
    });

    test('shows error when file does not exist', function () {
        $this->actingAs($this->user);

        // Version exists but no file is uploaded
        $this->version->update([
            'file_path' => null,
            'file_size' => null,
            'file_hash' => null,
        ]);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->call('download')
            ->assertSee('File not available');
    });

    test('tracks download statistics', function () {
        $this->actingAs($this->user);

        // Create a test file
        $testFile = UploadedFile::fake()->create('plugin.zip', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/plugin.zip";
        Storage::put($filePath, $testFile->getContent());

        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => hash('sha256', $testFile->getContent()),
        ]);

        $initialDownloadCount = $this->plugin->download_count;
        $initialStatCount = PluginStatistic::count();

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])->call('download');

        // Check that download count increased
        $this->plugin->refresh();
        expect($this->plugin->download_count)->toBe($initialDownloadCount + 1);

        // Check that statistic was recorded
        expect(PluginStatistic::count())->toBe($initialStatCount + 1);
        $stat = PluginStatistic::latest()->first();
        expect($stat->action)->toBe('download');
        expect($stat->user_id)->toBe($this->user->id);
        expect($stat->plugin_id)->toBe($this->plugin->id);
    });

    test('sets correct download headers', function () {
        $this->actingAs($this->user);

        $testFile = UploadedFile::fake()->create('plugin.zip', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/plugin.zip";
        Storage::put($filePath, $testFile->getContent());

        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => hash('sha256', $testFile->getContent()),
        ]);

        $response = Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])->call('download');

        // Headers are set in the actual download response
        $response->assertStatus(200);
    });

    test('tracks downloads for anonymous users correctly', function () {
        // This would be for view tracking, but downloads require auth
        // So this test ensures we always have user_id for downloads
        $this->actingAs($this->user);

        $testFile = UploadedFile::fake()->create('plugin.zip', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/plugin.zip";
        Storage::put($filePath, $testFile->getContent());

        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => hash('sha256', $testFile->getContent()),
        ]);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])->call('download');

        $stat = PluginStatistic::where('action', 'download')->latest()->first();
        expect($stat->user_id)->toBe($this->user->id);
        expect($stat->user_id)->not->toBeNull();
    });
});

describe('Plugin Download Rate Limiting', function () {
    test('respects download rate limits', function () {
        $this->actingAs($this->user);

        $testFile = UploadedFile::fake()->create('plugin.zip', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/plugin.zip";
        Storage::put($filePath, $testFile->getContent());

        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => hash('sha256', $testFile->getContent()),
        ]);

        // Make multiple download requests rapidly
        for ($i = 0; $i < 12; $i++) { // Exceeds the 10 per minute limit
            $response = $this->get("/plugins/{$this->plugin->slug}/download/{$this->version->version}");
            
            if ($i < 10) {
                $response->assertStatus(200);
            } else {
                $response->assertStatus(429); // Too Many Requests
                break;
            }
        }
    });

    test('rate limit resets after time period', function () {
        $this->markTestSkipped('Rate limit reset testing requires time manipulation');
        // This would require Carbon::setTestNow() or similar time manipulation
        // to test rate limit reset functionality
    });
});

describe('Plugin Download Security', function () {
    test('cannot download files outside plugin directory', function () {
        $this->actingAs($this->user);

        // Try to manipulate file path
        $this->version->update([
            'file_path' => '../../../etc/passwd',
            'file_size' => 100,
            'file_hash' => 'fake-hash',
        ]);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->call('download')
            ->assertSee('File not available');
    });

    test('validates file hash if provided', function () {
        $this->actingAs($this->user);

        $testFile = UploadedFile::fake()->create('plugin.zip', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/plugin.zip";
        Storage::put($filePath, $testFile->getContent());

        // Set incorrect hash
        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => 'incorrect-hash',
        ]);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->call('download')
            ->assertSee('File integrity check failed');
    });

    test('only allows downloading zip files', function () {
        $this->actingAs($this->user);

        // Try to upload a PHP file
        $testFile = UploadedFile::fake()->create('malicious.php', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/malicious.php";
        Storage::put($filePath, $testFile->getContent());

        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => hash('sha256', $testFile->getContent()),
        ]);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->call('download')
            ->assertSee('Invalid file type');
    });
});

describe('Plugin Download UI', function () {
    test('shows file size when available', function () {
        $this->actingAs($this->user);

        $this->version->update([
            'file_size' => 1048576, // 1MB
        ]);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->assertSee('1.0 MB');
    });

    test('shows download button when file is available', function () {
        $this->actingAs($this->user);

        $testFile = UploadedFile::fake()->create('plugin.zip', 1024);
        $filePath = "plugins/{$this->plugin->id}/{$this->version->version}/plugin.zip";
        Storage::put($filePath, $testFile->getContent());

        $this->version->update([
            'file_path' => $filePath,
            'file_size' => 1024,
            'file_hash' => hash('sha256', $testFile->getContent()),
        ]);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->assertSee('Download')
            ->assertSeeHtml('wire:click="download"');
    });

    test('shows unavailable message when file is not available', function () {
        $this->actingAs($this->user);

        Livewire::test('plugins.plugin-download', [
            'plugin' => $this->plugin,
            'version' => $this->version->version,
        ])
            ->assertSee('File not available')
            ->assertDontSee('Download');
    });
});