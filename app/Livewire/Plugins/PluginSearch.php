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

    public $search = '';

    public $selectedGroup = '';

    public $sortBy = 'relevance';

    public $featuredOnly = false;

    public $perPage = 12;

    public $minQueryLength = 2;

    protected $queryString = [
        'query' => ['except' => ''],
        'search' => ['except' => ''],
        'selectedGroup' => ['except' => ''],
        'sortBy' => ['except' => 'relevance'],
        'featuredOnly' => ['except' => false],
    ];

    /**
     * Reset pagination when search parameters change.
     */
    public function updating($property, $value)
    {
        // Sync query and search properties
        if ($property === 'query') {
            $this->search = $value;
        } elseif ($property === 'search') {
            $this->query = $value;
        }

        if (in_array($property, ['query', 'search', 'selectedGroup', 'sortBy', 'featuredOnly'])) {
            $this->resetPage();
        }
    }

    /**
     * Clear all search filters.
     */
    public function clearFilters()
    {
        $this->reset(['query', 'search', 'selectedGroup', 'sortBy', 'featuredOnly']);
        $this->resetPage();
    }

    /**
     * Get search results using Scout (if configured) or fallback to database search.
     */
    public function getResultsProperty()
    {
        $searchQuery = $this->query ?: $this->search;
        $hasFilters = $this->selectedGroup || $this->featuredOnly || ($this->sortBy !== 'relevance');
        
        // Require minimum query length unless filters are applied
        if (strlen($searchQuery) < $this->minQueryLength && !$hasFilters) {
            return collect([]);
        }

        // Try Scout search first, fallback to database search
        try {
            $searchQuery = $this->query ?: $this->search;
            // Use empty search query if only filters are applied
            $scoutQuery = strlen($searchQuery) >= $this->minQueryLength ? $searchQuery : '*';
            $results = Plugin::search($scoutQuery, function ($meilisearch, $query, $options) {
                // Apply filters
                $filters = ['status = active'];

                if ($this->selectedGroup) {
                    $filters[] = 'plugin_group_id = '.$this->selectedGroup;
                }

                if ($this->featuredOnly) {
                    $filters[] = 'featured = 1';
                }

                $options['filter'] = $filters;

                // Apply sorting
                if ($this->sortBy !== 'relevance') {
                    switch ($this->sortBy) {
                        case 'downloads':
                            $options['sort'] = ['download_count:desc'];
                            break;
                        case 'views':
                            $options['sort'] = ['view_count:desc'];
                            break;
                        case 'name':
                            $options['sort'] = ['name:asc'];
                            break;
                        case 'latest':
                            $options['sort'] = ['created_at:desc'];
                            break;
                    }
                }

                return $meilisearch->search($query, $options);
            });

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
            ->where('status', 'open');

        // Apply text search if query is provided
        $searchQuery = $this->query ?: $this->search;
        if (strlen($searchQuery) >= $this->minQueryLength) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%'.$searchQuery.'%')
                    ->orWhere('description', 'like', '%'.$searchQuery.'%');
            });
        }

        // Apply group filter
        if ($this->selectedGroup) {
            $query->byGroup($this->selectedGroup);
        }

        // Apply featured filter
        if ($this->featuredOnly) {
            $query->where('featured', true);
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
        $searchQuery = $this->query ?: $this->search;
        $hasQuery = strlen($searchQuery) >= $this->minQueryLength;
        $hasFilters = $this->selectedGroup || $this->featuredOnly || ($this->sortBy !== 'relevance');
        $showResults = $hasQuery || $hasFilters;

        return view('livewire.plugins.plugin-search', [
            'results' => $results,
            'groups' => $groups,
            'hasQuery' => $showResults,
            'resultCount' => $showResults ? $results->total() : 0,
        ])->layout('layouts.app', [
            'title' => $hasQuery ? "Search Results for \"$searchQuery\"" : 'Search Plugins',
        ]);
    }
}
