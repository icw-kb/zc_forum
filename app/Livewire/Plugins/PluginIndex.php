<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use App\Services\PluginCacheService;
use Livewire\Component;
use Livewire\WithPagination;

class PluginIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedGroup = '';

    public $sortBy = 'latest';

    public $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedGroup' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    /**
     * Reset pagination when search or filters change.
     */
    public function updating($property)
    {
        if (in_array($property, ['search', 'selectedGroup', 'sortBy'])) {
            $this->resetPage();
        }
    }

    /**
     * Clear all filters.
     */
    public function clearFilters()
    {
        $this->reset(['search', 'selectedGroup', 'sortBy']);
        $this->resetPage();
    }

    public function render()
    {
        $cacheService = app(PluginCacheService::class);

        // Build cache key based on current filters
        $filters = [
            'search' => $this->search,
            'group' => $this->selectedGroup,
            'sort' => $this->sortBy,
            'perPage' => $this->perPage,
            'page' => $this->getPage(),
        ];

        $cacheKey = $cacheService->getPluginListingCacheKey($filters);

        // Cache the plugin query results
        $plugins = $cacheService->cachePluginListing($cacheKey, function () {
            $query = Plugin::query()
                ->with(['group', 'versions'])
                ->where('status', 'open');

            // Apply search filter
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            }

            // Apply group filter
            if ($this->selectedGroup) {
                $query->byGroup($this->selectedGroup);
            }

            // Apply sorting
            switch ($this->sortBy) {
                case 'downloads':
                    $query->mostDownloaded();
                    break;
                case 'views':
                    $query->mostViewed();
                    break;
                case 'name':
                    $query->orderBy('name');
                    break;
                case 'featured':
                    $query->featured()->latest();
                    break;
                default:
                    $query->latest();
            }

            return $query->paginate($this->perPage);
        }, 300); // 5 minutes for paginated results

        // Use cached data for groups and featured plugins
        $groups = $cacheService->getPluginGroups();
        $featuredPlugins = $cacheService->getFeaturedPlugins(3);

        return view('livewire.plugins.plugin-index', [
            'plugins' => $plugins,
            'groups' => $groups,
            'featuredPlugins' => $featuredPlugins,
        ])->layout('layouts.app', ['title' => 'Plugins']);
    }
}
