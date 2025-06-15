<?php

namespace App\Livewire\Plugins;

use App\Models\Plugin;
use App\Models\PluginGroup;
use Livewire\Component;
use Livewire\WithPagination;

class PluginSearch extends Component
{
    use WithPagination;

    public $query = '';

    public $selectedGroup = '';

    public $sortBy = 'relevance';

    public $perPage = 12;

    public $minQueryLength = 2;

    protected $queryString = [
        'query' => ['except' => ''],
        'selectedGroup' => ['except' => ''],
        'sortBy' => ['except' => 'relevance'],
    ];

    /**
     * Reset pagination when search parameters change.
     */
    public function updating($property)
    {
        if (in_array($property, ['query', 'selectedGroup', 'sortBy'])) {
            $this->resetPage();
        }
    }

    /**
     * Clear all search filters.
     */
    public function clearFilters()
    {
        $this->reset(['query', 'selectedGroup', 'sortBy']);
        $this->resetPage();
    }

    /**
     * Get search results using Scout (if configured) or fallback to database search.
     */
    public function getResultsProperty()
    {
        if (strlen($this->query) < $this->minQueryLength) {
            return collect([]);
        }

        // Try Scout search first, fallback to database search
        try {
            $results = Plugin::search($this->query)
                ->where('status', 'active');

            // Apply group filter if selected
            if ($this->selectedGroup) {
                $results = $results->where('plugin_group_id', $this->selectedGroup);
            }

            return $results->paginate($this->perPage);
        } catch (\Exception $e) {
            // Fallback to database search if Scout is not configured
            return $this->getDatabaseSearchResults();
        }
    }

    /**
     * Database fallback search when Scout is not available.
     */
    private function getDatabaseSearchResults()
    {
        $query = Plugin::query()
            ->with(['group', 'versions'])
            ->where('status', 'active');

        // Apply text search
        $query->where(function ($q) {
            $q->where('name', 'like', '%'.$this->query.'%')
                ->orWhere('description', 'like', '%'.$this->query.'%');
        });

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
            case 'latest':
                $query->latest();
                break;
            default: // relevance - for database search, we'll use name similarity
                $query->orderByRaw('CASE WHEN name LIKE ? THEN 1 ELSE 2 END', ['%'.$this->query.'%'])
                    ->orderBy('download_count', 'desc');
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $results = $this->results;
        $groups = PluginGroup::orderByPluginCount()->get();
        $hasQuery = strlen($this->query) >= $this->minQueryLength;

        return view('livewire.plugins.plugin-search', [
            'results' => $results,
            'groups' => $groups,
            'hasQuery' => $hasQuery,
            'resultCount' => $hasQuery ? $results->total() : 0,
        ])->layout('layouts.app', [
            'title' => $hasQuery ? "Search Results for \"{$this->query}\"" : 'Search Plugins',
        ]);
    }
}
