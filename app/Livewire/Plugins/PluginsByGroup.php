<?php

namespace App\Livewire\Plugins;

use App\Models\PluginGroup;
use App\Services\PluginCacheService;
use Livewire\Component;
use Livewire\WithPagination;

class PluginsByGroup extends Component
{
    use WithPagination;

    public PluginGroup $group;

    public $search = '';

    public $sortBy = 'latest';

    public $perPage = 12;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function mount(PluginGroup $group)
    {
        $this->group = $group;
    }

    /**
     * Reset pagination when search or sort changes.
     */
    public function updating($property)
    {
        if (in_array($property, ['search', 'sortBy'])) {
            $this->resetPage();
        }
    }

    /**
     * Clear search filter.
     */
    public function clearSearch()
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function render()
    {
        $cacheService = app(PluginCacheService::class);

        // Build cache key for group-specific listing
        $filters = [
            'search' => $this->search,
            'sort' => $this->sortBy,
            'perPage' => $this->perPage,
            'page' => $this->getPage(),
        ];

        $cacheKey = $cacheService->getGroupPluginsCacheKey($this->group->id, $filters);

        // Cache the plugin query results
        $plugins = $cacheService->cachePluginListing($cacheKey, function () {
            $query = $this->group->plugins()
                ->with(['versions'])
                ->where('status', 'open');

            // Apply search filter
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
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
                case 'is_featured':
                    $query->featured()->latest();
                    break;
                default:
                    $query->latest();
            }

            return $query->paginate($this->perPage);
        }, 300); // 5 minutes for paginated results

        // Use cached data for groups
        $allGroups = $cacheService->getPluginGroups();

        return view('livewire.plugins.plugins-by-group', [
            'plugins' => $plugins,
            'allGroups' => $allGroups,
        ])->layout('layouts.app', [
            'title' => $this->group->name.' Plugins',
        ]);
    }
}
