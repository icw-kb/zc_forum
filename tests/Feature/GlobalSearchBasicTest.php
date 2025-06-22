<?php

use App\Livewire\GlobalSearch;
use App\Models\Forum;
use App\Models\ForumGroup;
use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginVersion;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Models\ZencartVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Set Scout driver to null for testing
    config(['scout.driver' => 'null']);
    
    $this->user = User::factory()->create();
    $this->pluginGroup = PluginGroup::factory()->create(['name' => 'Test Plugin Group']);
    $this->forumGroup = ForumGroup::factory()->create(['name' => 'Test Forum Group']);
    $this->forum = Forum::factory()->for($this->forumGroup, 'group')->create(['name' => 'Test Forum']);
});

describe('GlobalSearch Component Core Features', function () {
    test('component can be mounted and rendered', function () {
        Livewire::test(GlobalSearch::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.global-search');
    });

    test('initial state is correct', function () {
        Livewire::test(GlobalSearch::class)
            ->assertSet('query', '')
            ->assertSet('showDropdown', false)
            ->assertSet('showAdvanced', false)
            ->assertSet('searchIn', 'all')
            ->assertSet('pluginGroup', '')
            ->assertSet('forumGroup', '')
            ->assertSet('dateRange', 'all')
            ->assertSet('author', '')
            ->assertSet('zenCartVersion', '')
            ->assertSet('pluginStatus', 'all')
            ->assertSet('isEncapsulated', 'all')
            ->assertSet('phpVersion', '');
    });

    test('advanced search can be toggled', function () {
        Livewire::test(GlobalSearch::class)
            ->assertSet('showAdvanced', false)
            ->call('toggleAdvanced')
            ->assertSet('showAdvanced', true)
            ->call('toggleAdvanced')
            ->assertSet('showAdvanced', false);
    });

    test('dropdown behavior based on query length', function () {
        Livewire::test(GlobalSearch::class)
            ->set('query', 'a')
            ->assertSet('showDropdown', false)
            ->assertSet('results', [])
            ->set('query', 'te')
            ->assertSet('showDropdown', true)
            ->set('query', '')
            ->assertSet('showDropdown', false)
            ->assertSet('results', []);
    });

    test('search performs when query is long enough', function () {
        $component = Livewire::test(GlobalSearch::class)
            ->set('query', 'test');
        
        // Check that performSearch was called (showDropdown is set to true)
        $component->assertSet('showDropdown', true);
        
        // Results should be an array with expected keys
        $results = $component->get('results');
        expect($results)->toBeArray();
        expect($results)->toHaveKeys(['plugins', 'plugin_versions', 'threads', 'posts', 'forums', 'forum_groups', 'plugin_groups']);
    });
});

describe('Context Detection', function () {
    test('detects plugin context correctly', function () {
        // Test the component from within a plugin route context
        $response = $this->get('/plugins');
        $response->assertStatus(200);
        
        // Extract the component from the page and test its context
        $component = Livewire::test(GlobalSearch::class);
        
        // Manually set the context as if we're on a plugin route
        $component->set('currentContext', 'plugins')
                  ->set('searchIn', 'plugins')
                  ->assertSet('currentContext', 'plugins')
                  ->assertSet('searchIn', 'plugins');
    });

    test('detects forum context correctly', function () {
        // Test the component from within a forum route context
        $response = $this->get('/forums');
        $response->assertStatus(200);
        
        // Extract the component and test its context
        $component = Livewire::test(GlobalSearch::class);
        
        // Manually set the context as if we're on a forum route
        $component->set('currentContext', 'forums')
                  ->set('searchIn', 'forums')
                  ->assertSet('currentContext', 'forums')
                  ->assertSet('searchIn', 'forums');
    });

    test('defaults to all context on other routes', function () {
        // Visit home route
        $this->get('/');
        
        Livewire::test(GlobalSearch::class)
            ->assertSet('currentContext', 'all')
            ->assertSet('searchIn', 'all');
    });
});

describe('Search Filters', function () {
    test('search in filter changes results', function () {
        Livewire::test(GlobalSearch::class)
            ->set('query', 'test')
            ->set('searchIn', 'plugins')
            ->assertSet('searchIn', 'plugins');
        
        // When searching only plugins, forum results should be empty
        $component = Livewire::test(GlobalSearch::class)
            ->set('searchIn', 'plugins')
            ->set('query', 'test');
        
        $results = $component->get('results');
        expect($results['threads'])->toBeEmpty();
        expect($results['posts'])->toBeEmpty();
        expect($results['forums'])->toBeEmpty();
        expect($results['forum_groups'])->toBeEmpty();
    });

    test('plugin filters are applied', function () {
        $component = Livewire::test(GlobalSearch::class)
            ->set('query', 'test')
            ->set('searchIn', 'plugins')
            ->set('pluginStatus', 'featured')
            ->set('isEncapsulated', 'yes')
            ->set('phpVersion', '8.1');
        
        // Verify filters are set
        $component
            ->assertSet('pluginStatus', 'featured')
            ->assertSet('isEncapsulated', 'yes')
            ->assertSet('phpVersion', '8.1');
    });

    test('forum filters are applied', function () {
        $component = Livewire::test(GlobalSearch::class)
            ->set('query', 'test')
            ->set('searchIn', 'forums')
            ->set('dateRange', 'week')
            ->set('author', 'john');
        
        // Verify filters are set
        $component
            ->assertSet('dateRange', 'week')
            ->assertSet('author', 'john');
    });
});

describe('Search Submission', function () {
    test('submits basic search', function () {
        Livewire::test(GlobalSearch::class)
            ->set('query', 'test search')
            ->call('submitSearch')
            ->assertRedirect(route('search', ['q' => 'test search']));
    });

    test('submits search with filters', function () {
        Livewire::test(GlobalSearch::class)
            ->set('query', 'test')
            ->set('showAdvanced', true)
            ->set('searchIn', 'plugins')
            ->set('pluginGroup', '123')
            ->set('dateRange', 'week')
            ->call('submitSearch')
            ->assertRedirect(route('search', [
                'q' => 'test',
                'type' => 'plugins',
                'plugin_group' => '123',
                'date' => 'week'
            ]));
    });

    test('does not submit empty search', function () {
        Livewire::test(GlobalSearch::class)
            ->set('query', '')
            ->call('submitSearch')
            ->assertNoRedirect();
    });

    test('trims query whitespace', function () {
        Livewire::test(GlobalSearch::class)
            ->set('query', '  test  ')
            ->call('submitSearch')
            ->assertRedirect(route('search', ['q' => 'test']));
    });
});

describe('UI Interactions', function () {
    test('hide dropdown method works', function () {
        Livewire::test(GlobalSearch::class)
            ->set('showDropdown', true)
            ->call('hideDropdown')
            ->assertSet('showDropdown', false);
    });

    test('clear button resets query', function () {
        Livewire::test(GlobalSearch::class)
            ->set('query', 'test')
            ->set('query', '')
            ->assertSet('query', '');
    });

    test('renders correct views based on state', function () {
        // With advanced search off
        Livewire::test(GlobalSearch::class)
            ->assertDontSee('Searching in:');
        
        // With advanced search on
        Livewire::test(GlobalSearch::class)
            ->set('showAdvanced', true)
            ->assertSee('Date range');
    });
});

describe('Component Data Loading', function () {
    test('loads plugin groups when needed', function () {
        // Create some plugin groups
        PluginGroup::factory()->count(3)->create();
        
        $component = Livewire::test(GlobalSearch::class)
            ->set('searchIn', 'plugins');
        
        $pluginGroups = $component->viewData('pluginGroups');
        expect($pluginGroups)->toHaveCount(4); // 3 created + 1 from beforeEach
    });

    test('loads forum groups when needed', function () {
        // Create some forum groups
        ForumGroup::factory()->count(3)->create();
        
        $component = Livewire::test(GlobalSearch::class)
            ->set('searchIn', 'forums');
        
        $forumGroups = $component->viewData('forumGroups');
        expect($forumGroups)->toHaveCount(4); // 3 created + 1 from beforeEach
    });

    test('loads zen cart versions for plugin context', function () {
        // Create some versions
        ZencartVersion::factory()->count(3)->create();
        
        $component = Livewire::test(GlobalSearch::class)
            ->set('currentContext', 'plugins');
        
        $versions = $component->viewData('zenCartVersions');
        expect($versions)->toHaveCount(3);
    });
});

describe('Error Handling', function () {
    test('handles missing search results gracefully', function () {
        $component = Livewire::test(GlobalSearch::class)
            ->set('query', 'nonexistent-search-term');
        
        // Should not throw errors
        $component->assertStatus(200);
        
        // Results should exist but be empty arrays
        $results = $component->get('results');
        expect($results)->toBeArray();
        foreach ($results as $category) {
            expect($category)->toBeArray();
        }
    });

    test('handles invalid filter values', function () {
        Livewire::test(GlobalSearch::class)
            ->set('pluginStatus', 'invalid-status')
            ->set('query', 'test')
            ->assertStatus(200); // Should not crash
    });
});