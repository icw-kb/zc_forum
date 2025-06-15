<?php

use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginStatistic;
use App\Models\PluginVersion;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->group = PluginGroup::factory()->create(['name' => 'Test Group']);
    $this->user = User::factory()->create();
});

describe('Plugin Index Page', function () {
    test('can view plugin index page', function () {
        $response = $this->get('/plugins');

        $response->assertStatus(200);
        $response->assertSeeLivewire('plugins.plugin-index');
    });

    test('displays plugins in listing', function () {
        $plugins = Plugin::factory()
            ->count(3)
            ->for($this->group, 'group')
            ->active()
            ->create();

        Livewire::test('plugins.plugin-index')
            ->assertSee($plugins[0]->name)
            ->assertSee($plugins[1]->name)
            ->assertSee($plugins[2]->name);
    });

    test('displays plugin metadata correctly', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create([
                'name' => 'Test Plugin',
                'description' => 'Test Description',
                'view_count' => 100,
                'download_count' => 50,
                'featured' => true,
            ]);

        Livewire::test('plugins.plugin-index')
            ->assertSee('Test Plugin')
            ->assertSee('Test Description')
            ->assertSee('100') // view count
            ->assertSee('50') // download count
            ->assertSee('Featured'); // featured badge
    });

    test('can filter by group', function () {
        $group1 = PluginGroup::factory()->create(['name' => 'Group 1']);
        $group2 = PluginGroup::factory()->create(['name' => 'Group 2']);

        $plugin1 = Plugin::factory()->for($group1, 'group')->active()->create(['name' => 'Plugin 1']);
        $plugin2 = Plugin::factory()->for($group2, 'group')->active()->create(['name' => 'Plugin 2']);

        Livewire::test('plugins.plugin-index')
            ->set('selectedGroup', $group1->id)
            ->assertSee('Plugin 1')
            ->assertDontSee('Plugin 2');
    });

    test('can sort by different criteria', function () {
        $plugin1 = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create(['name' => 'A Plugin', 'download_count' => 10]);

        $plugin2 = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create(['name' => 'B Plugin', 'download_count' => 100]);

        // Test sorting by downloads (descending)
        Livewire::test('plugins.plugin-index')
            ->set('sortBy', 'downloads')
            ->assertSeeInOrder(['B Plugin', 'A Plugin']);

        // Test sorting by name (ascending)
        Livewire::test('plugins.plugin-index')
            ->set('sortBy', 'name')
            ->assertSeeInOrder(['A Plugin', 'B Plugin']);
    });

    test('shows featured badge for featured plugins', function () {
        $featuredPlugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->featured()
            ->create(['name' => 'Featured Plugin']);

        $regularPlugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create(['name' => 'Regular Plugin']);

        Livewire::test('plugins.plugin-index')
            ->assertSee('Featured Plugin')
            ->assertSee('Regular Plugin')
            ->assertSee('Featured'); // Should see featured badge
    });

    test('hides inactive plugins from listing', function () {
        $activePlugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create(['name' => 'Active Plugin']);

        $inactivePlugin = Plugin::factory()
            ->for($this->group, 'group')
            ->create(['name' => 'Inactive Plugin', 'status' => 'inactive']);

        Livewire::test('plugins.plugin-index')
            ->assertSee('Active Plugin')
            ->assertDontSee('Inactive Plugin');
    });

    test('shows pagination when there are many plugins', function () {
        Plugin::factory()
            ->count(20)
            ->for($this->group, 'group')
            ->active()
            ->create();

        Livewire::test('plugins.plugin-index')
            ->assertSee('Next'); // Pagination link
    });
});

describe('Plugin Show Page', function () {
    test('can view plugin detail page', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create();

        $response = $this->get("/plugins/{$plugin->slug}");

        $response->assertStatus(200);
        $response->assertSeeLivewire('plugins.plugin-show');
    });

    test('displays plugin details correctly', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create([
                'name' => 'Detailed Plugin',
                'description' => 'This is a detailed description',
                'github_url' => 'https://github.com/example/plugin',
            ]);

        Livewire::test('plugins.plugin-show', ['plugin' => $plugin])
            ->assertSee('Detailed Plugin')
            ->assertSee('This is a detailed description')
            ->assertSee('https://github.com/example/plugin');
    });

    test('displays plugin versions', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create();

        $version1 = PluginVersion::factory()
            ->for($plugin, 'plugin')
            ->create(['version' => '1.0.0']);

        $version2 = PluginVersion::factory()
            ->for($plugin, 'plugin')
            ->create(['version' => '2.0.0']);

        Livewire::test('plugins.plugin-show', ['plugin' => $plugin])
            ->assertSee('1.0.0')
            ->assertSee('2.0.0');
    });

    test('tracks plugin views', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create(['view_count' => 10]);

        $initialViewCount = $plugin->view_count;

        Livewire::test('plugins.plugin-show', ['plugin' => $plugin]);

        $plugin->refresh();
        expect($plugin->view_count)->toBe($initialViewCount + 1);
    });

    test('creates view statistic record', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create();

        $initialStatCount = PluginStatistic::count();

        Livewire::test('plugins.plugin-show', ['plugin' => $plugin]);

        expect(PluginStatistic::count())->toBe($initialStatCount + 1);
        expect(PluginStatistic::latest()->first()->action)->toBe('view');
    });

    test('prevents duplicate views within 1 hour', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->active()
            ->create(['view_count' => 10]);

        // First view
        Livewire::test('plugins.plugin-show', ['plugin' => $plugin]);

        $firstViewCount = $plugin->fresh()->view_count;
        $firstStatCount = PluginStatistic::count();

        // Second view immediately after (should be ignored)
        Livewire::test('plugins.plugin-show', ['plugin' => $plugin]);

        expect($plugin->fresh()->view_count)->toBe($firstViewCount);
        expect(PluginStatistic::count())->toBe($firstStatCount);
    });

    test('returns 404 for non-existent plugin', function () {
        $response = $this->get('/plugins/non-existent-plugin');

        $response->assertStatus(404);
    });

    test('returns 404 for inactive plugin', function () {
        $plugin = Plugin::factory()
            ->for($this->group, 'group')
            ->create(['status' => 'inactive']);

        $response = $this->get("/plugins/{$plugin->slug}");

        $response->assertStatus(404);
    });
});

describe('Plugins by Group Page', function () {
    test('can view plugins by group page', function () {
        $group = PluginGroup::factory()->create();

        $response = $this->get("/plugins/group/{$group->slug}");

        $response->assertStatus(200);
        $response->assertSeeLivewire('plugins.plugins-by-group');
    });

    test('displays plugins for specific group only', function () {
        $group1 = PluginGroup::factory()->create(['name' => 'Group 1']);
        $group2 = PluginGroup::factory()->create(['name' => 'Group 2']);

        $plugin1 = Plugin::factory()->for($group1, 'group')->active()->create(['name' => 'Plugin 1']);
        $plugin2 = Plugin::factory()->for($group2, 'group')->active()->create(['name' => 'Plugin 2']);

        Livewire::test('plugins.plugins-by-group', ['group' => $group1])
            ->assertSee('Plugin 1')
            ->assertDontSee('Plugin 2');
    });

    test('shows group information', function () {
        $group = PluginGroup::factory()->create([
            'name' => 'Display Test Group',
            'description' => 'Test group description',
        ]);

        Livewire::test('plugins.plugins-by-group', ['group' => $group])
            ->assertSee('Display Test Group')
            ->assertSee('Test group description');
    });

    test('returns 404 for non-existent group', function () {
        $response = $this->get('/plugins/group/non-existent-group');

        $response->assertStatus(404);
    });
});
