<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use App\Models\PluginGroup;
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
        $query = Plugin::query()
            ->with(['group', 'versions'])
            ->where('status', 'active');

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

        $plugins = $query->paginate($this->perPage);
        $groups = PluginGroup::orderByPluginCount()->get();
        $featuredPlugins = Plugin::featured()
            ->with(['group', 'versions'])
            ->take(3)
            ->get();

        return view('livewire.plugins.plugin-index', [
            'plugins' => $plugins,
            'groups' => $groups,
            'featuredPlugins' => $featuredPlugins,
        ])->layout('layouts.app', ['title' => 'Plugins']);
    }
}
