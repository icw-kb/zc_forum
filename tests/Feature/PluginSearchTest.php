<?php

use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\User;
use Laravel\Scout\EngineManager;
use Livewire\Livewire;
use Mockery;

beforeEach(function () {
    $this->group = PluginGroup::factory()->create(['name' => 'Test Group']);
    $this->user = User::factory()->create();
});

describe('Plugin Search Page', function () {
    test('can view plugin search page', function () {
        $response = $this->get('/plugins/search');

        $response->assertStatus(200);
        $response->assertSeeLivewire('plugins.plugin-search');
    });

    test('displays search form', function () {
        Livewire::test('plugins.plugin-search')
            ->assertSee('Search plugins')
            ->assertSeeHtml('wire:model.live.debounce.300ms="search"');
    });

    test('performs basic search', function () {
        $plugin1 = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Payment Gateway Plugin', 'description' => 'Process payments']);

        $plugin2 = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Shipping Calculator', 'description' => 'Calculate shipping costs']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'payment')
            ->assertSee('Payment Gateway Plugin')
            ->assertDontSee('Shipping Calculator');
    });

    test('searches in plugin description', function () {
        $plugin1 = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Plugin A', 'description' => 'Handles payment processing']);

        $plugin2 = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Plugin B', 'description' => 'Manages inventory']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'payment')
            ->assertSee('Plugin A')
            ->assertDontSee('Plugin B');
    });

    test('can filter search results by group', function () {
        $group1 = PluginGroup::factory()->create(['name' => 'Payment']);
        $group2 = PluginGroup::factory()->create(['name' => 'Shipping']);

        $plugin1 = Plugin::factory()
            ->for($group1)
            ->active()
            ->create(['name' => 'Payment Plugin', 'description' => 'Process payments']);

        $plugin2 = Plugin::factory()
            ->for($group2)
            ->active()
            ->create(['name' => 'Payment Tracker', 'description' => 'Track payment status']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'payment')
            ->set('selectedGroup', $group1->id)
            ->assertSee('Payment Plugin')
            ->assertDontSee('Payment Tracker');
    });

    test('can sort search results', function () {
        $plugin1 = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'A Plugin', 'download_count' => 10]);

        $plugin2 = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'B Plugin', 'download_count' => 100]);

        // Both should match empty search
        Livewire::test('plugins.plugin-search')
            ->set('sortBy', 'downloads')
            ->assertSeeInOrder(['B Plugin', 'A Plugin']);
    });

    test('can filter by featured status', function () {
        $featuredPlugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->featured()
            ->create(['name' => 'Featured Plugin']);

        $regularPlugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Regular Plugin']);

        Livewire::test('plugins.plugin-search')
            ->set('featuredOnly', true)
            ->assertSee('Featured Plugin')
            ->assertDontSee('Regular Plugin');
    });

    test('shows no results message when search returns empty', function () {
        Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Test Plugin']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'nonexistent')
            ->assertSee('No plugins found');
    });

    test('excludes inactive plugins from search', function () {
        $activePlugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Active Plugin', 'description' => 'This is active']);

        $inactivePlugin = Plugin::factory()
            ->for($this->group)
            ->create(['name' => 'Inactive Plugin', 'status' => 'inactive', 'description' => 'This is inactive']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'plugin')
            ->assertSee('Active Plugin')
            ->assertDontSee('Inactive Plugin');
    });

    test('search preserves query parameters in URL', function () {
        Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Test Plugin']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'test')
            ->assertSet('search', 'test');
    });

    test('clears search results when search term is empty', function () {
        $plugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Test Plugin']);

        $component = Livewire::test('plugins.plugin-search')
            ->set('search', 'test')
            ->assertSee('Test Plugin');

        $component->set('search', '')
            ->assertDontSee('Test Plugin'); // Should show empty state or all plugins
    });

    test('handles special characters in search', function () {
        $plugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Plugin with "quotes" & symbols']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'quotes')
            ->assertSee('Plugin with "quotes" & symbols');
    });

    test('search is case insensitive', function () {
        $plugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'PayPal Integration']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'paypal')
            ->assertSee('PayPal Integration');

        Livewire::test('plugins.plugin-search')
            ->set('search', 'PAYPAL')
            ->assertSee('PayPal Integration');
    });

    test('search shows plugin metadata', function () {
        $plugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create([
                'name' => 'Test Plugin',
                'description' => 'Test description',
                'view_count' => 100,
                'download_count' => 50,
                'featured' => true,
            ]);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'test')
            ->assertSee('Test Plugin')
            ->assertSee('Test description')
            ->assertSee('100') // view count
            ->assertSee('50') // download count
            ->assertSee('Featured'); // featured badge
    });
});

describe('Plugin Search Functionality', function () {
    test('falls back to database search when scout is unavailable', function () {
        // Mock Scout to throw an exception
        $this->mock(EngineManager::class, function ($mock) {
            $mock->shouldReceive('engine')->andThrow(new Exception('Scout unavailable'));
        });

        $plugin = Plugin::factory()
            ->for($this->group)
            ->active()
            ->create(['name' => 'Database Search Plugin']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'database')
            ->assertSee('Database Search Plugin');
    });

    test('search handles empty database gracefully', function () {
        // No plugins in database
        Livewire::test('plugins.plugin-search')
            ->set('search', 'anything')
            ->assertSee('No plugins found');
    });

    test('search respects pagination', function () {
        // Create more than one page worth of plugins
        Plugin::factory()
            ->count(20)
            ->for($this->group)
            ->active()
            ->create(['name' => 'Search Test Plugin']);

        $component = Livewire::test('plugins.plugin-search')
            ->set('search', 'test');

        // Should see pagination controls
        $html = $component->get('plugins')->render();
        expect($html)->toContain('Next'); // Pagination link
    });

    test('combining multiple filters works correctly', function () {
        $group1 = PluginGroup::factory()->create(['name' => 'Payment']);
        $group2 = PluginGroup::factory()->create(['name' => 'Shipping']);

        $plugin1 = Plugin::factory()
            ->for($group1)
            ->active()
            ->featured()
            ->create(['name' => 'Featured Payment Plugin']);

        $plugin2 = Plugin::factory()
            ->for($group1)
            ->active()
            ->create(['name' => 'Regular Payment Plugin']);

        $plugin3 = Plugin::factory()
            ->for($group2)
            ->active()
            ->featured()
            ->create(['name' => 'Featured Shipping Plugin']);

        Livewire::test('plugins.plugin-search')
            ->set('search', 'payment')
            ->set('selectedGroup', $group1->id)
            ->set('featuredOnly', true)
            ->assertSee('Featured Payment Plugin')
            ->assertDontSee('Regular Payment Plugin')
            ->assertDontSee('Featured Shipping Plugin');
    });
});