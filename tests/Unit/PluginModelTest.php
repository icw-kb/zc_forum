<?php

use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginStatistic;
use App\Models\PluginVersion;
use App\Models\User;

beforeEach(function () {
    $this->group = PluginGroup::factory()->create();
    $this->plugin = Plugin::factory()->for($this->group)->create();
    $this->user = User::factory()->create();
});

describe('Plugin Model Relationships', function () {
    test('belongs to plugin group', function () {
        expect($this->plugin->group)->toBeInstanceOf(PluginGroup::class);
        expect($this->plugin->group->id)->toBe($this->group->id);
    });

    test('has many versions', function () {
        $version1 = PluginVersion::factory()->for($this->plugin)->create();
        $version2 = PluginVersion::factory()->for($this->plugin)->create();

        expect($this->plugin->versions)->toHaveCount(2);
        expect($this->plugin->versions->first())->toBeInstanceOf(PluginVersion::class);
    });

    test('has many statistics', function () {
        $stat1 = PluginStatistic::factory()->for($this->plugin)->create();
        $stat2 = PluginStatistic::factory()->for($this->plugin)->create();

        expect($this->plugin->statistics)->toHaveCount(2);
        expect($this->plugin->statistics->first())->toBeInstanceOf(PluginStatistic::class);
    });
});

describe('Plugin Model Scopes', function () {
    test('featured scope returns only featured plugins', function () {
        $featuredPlugin = Plugin::factory()->for($this->group)->featured()->create();
        $regularPlugin = Plugin::factory()->for($this->group)->create(['featured' => false]);

        $featuredPlugins = Plugin::featured()->get();

        expect($featuredPlugins)->toHaveCount(1);
        expect($featuredPlugins->first()->id)->toBe($featuredPlugin->id);
    });

    test('by group scope filters by group id', function () {
        $group2 = PluginGroup::factory()->create();
        $plugin2 = Plugin::factory()->for($group2)->create();

        $groupPlugins = Plugin::byGroup($this->group->id)->get();

        expect($groupPlugins)->toHaveCount(1);
        expect($groupPlugins->first()->id)->toBe($this->plugin->id);
    });

    test('most downloaded scope orders by download count', function () {
        $plugin1 = Plugin::factory()->for($this->group)->create(['download_count' => 10]);
        $plugin2 = Plugin::factory()->for($this->group)->create(['download_count' => 100]);
        $plugin3 = Plugin::factory()->for($this->group)->create(['download_count' => 50]);

        $plugins = Plugin::mostDownloaded()->get();

        expect($plugins[0]->download_count)->toBe(100);
        expect($plugins[1]->download_count)->toBe(50);
        expect($plugins[2]->download_count)->toBe(10);
    });

    test('most viewed scope orders by view count', function () {
        $plugin1 = Plugin::factory()->for($this->group)->create(['view_count' => 20]);
        $plugin2 = Plugin::factory()->for($this->group)->create(['view_count' => 200]);
        $plugin3 = Plugin::factory()->for($this->group)->create(['view_count' => 100]);

        $plugins = Plugin::mostViewed()->get();

        expect($plugins[0]->view_count)->toBe(200);
        expect($plugins[1]->view_count)->toBe(100);
        expect($plugins[2]->view_count)->toBe(20);
    });

    test('with statistics scope includes statistics counts', function () {
        // Create statistics
        PluginStatistic::factory()->for($this->plugin)->view()->count(5)->create();
        PluginStatistic::factory()->for($this->plugin)->download()->count(3)->create();

        $plugin = Plugin::withStatistics()->find($this->plugin->id);

        expect($plugin->views_count)->toBe(5);
        expect($plugin->downloads_count)->toBe(3);
    });
});

describe('Plugin Model Methods', function () {
    test('increment view count increases view count', function () {
        $initialCount = $this->plugin->view_count;

        $this->plugin->incrementViewCount();

        expect($this->plugin->fresh()->view_count)->toBe($initialCount + 1);
    });

    test('increment download count increases download count', function () {
        $initialCount = $this->plugin->download_count;

        $this->plugin->incrementDownloadCount();

        expect($this->plugin->fresh()->download_count)->toBe($initialCount + 1);
    });

    test('record view creates statistic and increments count', function () {
        $initialCount = $this->plugin->view_count;
        $initialStatCount = PluginStatistic::count();

        $this->plugin->recordView($this->user->id, '127.0.0.1', 'Test User Agent');

        expect($this->plugin->fresh()->view_count)->toBe($initialCount + 1);
        expect(PluginStatistic::count())->toBe($initialStatCount + 1);

        $stat = PluginStatistic::latest()->first();
        expect($stat->plugin_id)->toBe($this->plugin->id);
        expect($stat->user_id)->toBe($this->user->id);
        expect($stat->action)->toBe('view');
        expect($stat->ip_address)->toBe('127.0.0.1');
        expect($stat->user_agent)->toBe('Test User Agent');
    });

    test('record view prevents duplicate views within 1 hour', function () {
        $initialCount = $this->plugin->view_count;
        $initialStatCount = PluginStatistic::count();

        // First view
        $this->plugin->recordView($this->user->id, '127.0.0.1', 'Test User Agent');

        $firstViewCount = $this->plugin->fresh()->view_count;
        $firstStatCount = PluginStatistic::count();

        // Second view immediately (should be ignored)
        $this->plugin->recordView($this->user->id, '127.0.0.1', 'Test User Agent');

        expect($this->plugin->fresh()->view_count)->toBe($firstViewCount);
        expect(PluginStatistic::count())->toBe($firstStatCount);
    });

    test('record view allows different users to view', function () {
        $user2 = User::factory()->create();
        $initialCount = $this->plugin->view_count;

        $this->plugin->recordView($this->user->id, '127.0.0.1', 'User Agent 1');
        $this->plugin->recordView($user2->id, '127.0.0.1', 'User Agent 2');

        expect($this->plugin->fresh()->view_count)->toBe($initialCount + 2);
        expect(PluginStatistic::count())->toBe(2);
    });

    test('record view allows different IP addresses', function () {
        $initialCount = $this->plugin->view_count;

        $this->plugin->recordView($this->user->id, '127.0.0.1', 'Test User Agent');
        $this->plugin->recordView($this->user->id, '192.168.1.1', 'Test User Agent');

        expect($this->plugin->fresh()->view_count)->toBe($initialCount + 2);
        expect(PluginStatistic::count())->toBe(2);
    });

    test('record download creates statistic and increments count', function () {
        $initialCount = $this->plugin->download_count;
        $initialStatCount = PluginStatistic::count();

        $this->plugin->recordDownload($this->user->id, '127.0.0.1', 'Test User Agent');

        expect($this->plugin->fresh()->download_count)->toBe($initialCount + 1);
        expect(PluginStatistic::count())->toBe($initialStatCount + 1);

        $stat = PluginStatistic::latest()->first();
        expect($stat->plugin_id)->toBe($this->plugin->id);
        expect($stat->user_id)->toBe($this->user->id);
        expect($stat->action)->toBe('download');
        expect($stat->ip_address)->toBe('127.0.0.1');
        expect($stat->user_agent)->toBe('Test User Agent');
    });

    test('record download allows multiple downloads by same user', function () {
        $initialCount = $this->plugin->download_count;

        $this->plugin->recordDownload($this->user->id, '127.0.0.1', 'Test User Agent');
        $this->plugin->recordDownload($this->user->id, '127.0.0.1', 'Test User Agent');

        expect($this->plugin->fresh()->download_count)->toBe($initialCount + 2);
        expect(PluginStatistic::count())->toBe(2);
    });

    test('has versions returns true when plugin has versions', function () {
        PluginVersion::factory()->for($this->plugin)->create();

        expect($this->plugin->hasVersions())->toBeTrue();
    });

    test('has versions returns false when plugin has no versions', function () {
        expect($this->plugin->hasVersions())->toBeFalse();
    });

    test('latest version attribute returns latest version', function () {
        $version1 = PluginVersion::factory()->for($this->plugin)->create(['version' => '1.0.0']);
        $version2 = PluginVersion::factory()->for($this->plugin)->create(['version' => '2.0.0']);

        // The latest version should be the most recently created
        expect($this->plugin->latest_version->id)->toBe($version2->id);
    });

    test('latest version attribute returns null when no versions', function () {
        expect($this->plugin->latest_version)->toBeNull();
    });
});

describe('Plugin Model Search Configuration', function () {
    test('to searchable array includes correct fields', function () {
        $this->plugin->update([
            'name' => 'Test Plugin',
            'description' => 'Test Description',
            'status' => 'active',
            'featured' => true,
            'view_count' => 100,
            'download_count' => 50,
            'github_url' => 'https://github.com/test/plugin',
        ]);

        $searchableArray = $this->plugin->toSearchableArray();

        expect($searchableArray)->toHaveKey('id');
        expect($searchableArray)->toHaveKey('name');
        expect($searchableArray)->toHaveKey('slug');
        expect($searchableArray)->toHaveKey('description');
        expect($searchableArray)->toHaveKey('status');
        expect($searchableArray)->toHaveKey('featured');
        expect($searchableArray)->toHaveKey('view_count');
        expect($searchableArray)->toHaveKey('download_count');
        expect($searchableArray)->toHaveKey('plugin_group_id');
        expect($searchableArray)->toHaveKey('group_name');
        expect($searchableArray)->toHaveKey('github_url');
        expect($searchableArray)->toHaveKey('created_at');
        expect($searchableArray)->toHaveKey('updated_at');

        expect($searchableArray['name'])->toBe('Test Plugin');
        expect($searchableArray['description'])->toBe('Test Description');
        expect($searchableArray['status'])->toBe('active');
        expect($searchableArray['featured'])->toBeTrue();
        expect($searchableArray['view_count'])->toBe(100);
        expect($searchableArray['download_count'])->toBe(50);
        expect($searchableArray['group_name'])->toBe($this->group->name);
    });

    test('should be searchable returns true for active plugins', function () {
        $this->plugin->update(['status' => 'active']);

        expect($this->plugin->shouldBeSearchable())->toBeTrue();
    });

    test('should be searchable returns false for inactive plugins', function () {
        $this->plugin->update(['status' => 'inactive']);

        expect($this->plugin->shouldBeSearchable())->toBeFalse();
    });

    test('should be searchable returns false for deprecated plugins', function () {
        $this->plugin->update(['status' => 'deprecated']);

        expect($this->plugin->shouldBeSearchable())->toBeFalse();
    });
});

describe('Plugin Model Attributes', function () {
    test('casts featured as boolean', function () {
        $this->plugin->update(['featured' => 1]);
        expect($this->plugin->fresh()->featured)->toBeTrue();

        $this->plugin->update(['featured' => 0]);
        expect($this->plugin->fresh()->featured)->toBeFalse();
    });

    test('casts view count as integer', function () {
        $this->plugin->update(['view_count' => '100']);
        expect($this->plugin->fresh()->view_count)->toBe(100);
        expect($this->plugin->fresh()->view_count)->toBeInt();
    });

    test('casts download count as integer', function () {
        $this->plugin->update(['download_count' => '50']);
        expect($this->plugin->fresh()->download_count)->toBe(50);
        expect($this->plugin->fresh()->download_count)->toBeInt();
    });

    test('generates slug automatically', function () {
        $plugin = Plugin::factory()->for($this->group)->create(['name' => 'My Awesome Plugin']);

        expect($plugin->slug)->toBe('my-awesome-plugin');
    });

    test('ensures unique slugs', function () {
        $plugin1 = Plugin::factory()->for($this->group)->create(['name' => 'Duplicate Name']);
        $plugin2 = Plugin::factory()->for($this->group)->create(['name' => 'Duplicate Name']);

        expect($plugin1->slug)->toBe('duplicate-name');
        expect($plugin2->slug)->not->toBe('duplicate-name');
        expect($plugin2->slug)->toContain('duplicate-name');
    });
});