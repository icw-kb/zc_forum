<?php

namespace App\Livewire;

use App\Models\Forum;
use App\Models\ForumGroup;
use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginVersion;
use App\Models\Post;
use App\Models\Thread;
use App\Models\ZencartVersion;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';

    public $showDropdown = false;

    public $results = [];

    public $isLoading = false;

    public $showAdvanced = false;

    // Advanced search filters
    public $searchIn = 'all'; // all, plugins, forums

    public $pluginGroup = '';

    public $forumGroup = '';

    public $dateRange = 'all'; // all, today, week, month, year

    public $author = '';

    // Plugin-specific filters
    public $zenCartVersion = '';

    public $pluginStatus = 'all'; // all, featured, new, popular

    public $isEncapsulated = 'all'; // all, yes, no

    public $phpVersion = '';

    // Context detection
    public $currentContext = 'all';

    protected $listeners = ['hideSearchDropdown'];

    public function mount()
    {
        // Detect current context based on route
        if (request()->routeIs('plugins.*')) {
            $this->currentContext = 'plugins';
            $this->searchIn = 'plugins';
        } elseif (request()->routeIs('forums.*')) {
            $this->currentContext = 'forums';
            $this->searchIn = 'forums';
        }
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->showDropdown = false;
            $this->results = [];

            return;
        }

        $this->isLoading = true;
        $this->performSearch();
        $this->showDropdown = true;
        $this->isLoading = false;
    }

    public function performSearch()
    {
        $query = trim($this->query);

        if (strlen($query) < 2) {
            $this->results = [];

            return;
        }

        // Apply filters based on searchIn selection
        if ($this->searchIn === 'plugins') {
            $this->results = [
                'plugins' => $this->searchPlugins($query),
                'plugin_versions' => $this->searchPluginVersions($query),
                'plugin_groups' => $this->searchPluginGroups($query),
                'threads' => [],
                'posts' => [],
                'forums' => [],
                'forum_groups' => [],
            ];
        } elseif ($this->searchIn === 'forums') {
            $this->results = [
                'plugins' => [],
                'plugin_versions' => [],
                'plugin_groups' => [],
                'threads' => $this->searchThreads($query),
                'posts' => $this->searchPosts($query),
                'forums' => $this->searchForums($query),
                'forum_groups' => $this->searchForumGroups($query),
            ];
        } else {
            // Search all
            $this->results = [
                'plugins' => $this->searchPlugins($query),
                'plugin_versions' => $this->searchPluginVersions($query),
                'threads' => $this->searchThreads($query),
                'posts' => $this->searchPosts($query),
                'forums' => $this->searchForums($query),
                'forum_groups' => $this->searchForumGroups($query),
                'plugin_groups' => $this->searchPluginGroups($query),
            ];
        }
    }

    private function searchPlugins($query, $limit = 3)
    {
        $searchQuery = Plugin::search($query);

        // Build filter query
        $filterQuery = Plugin::query();

        // Apply plugin group filter if set
        if ($this->pluginGroup) {
            $filterQuery->where('plugin_group_id', $this->pluginGroup);
        }

        // Apply status filter
        if ($this->pluginStatus !== 'all') {
            switch ($this->pluginStatus) {
                case 'featured':
                    $filterQuery->where('is_featured', true);
                    break;
                case 'new':
                    $filterQuery->where('created_at', '>=', now()->subDays(30));
                    break;
                case 'popular':
                    $filterQuery->orderBy('download_count', 'desc');
                    break;
            }
        }

        // Apply date range filter for plugins
        if ($this->dateRange !== 'all' && ($this->searchIn === 'plugins' || $this->searchIn === 'all')) {
            $date = match ($this->dateRange) {
                'today' => now()->startOfDay(),
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => null,
            };

            if ($date) {
                $filterQuery->where('created_at', '>=', $date);
            }
        }

        // Apply file and compatibility filters through plugin versions
        if ($this->zenCartVersion || $this->isEncapsulated !== 'all' || $this->phpVersion) {
            $filterQuery->whereHas('versions', function ($versionQuery) {
                if ($this->zenCartVersion) {
                    $versionQuery->whereHas('compatibleZenCartVersions', function ($zcQuery) {
                        $zcQuery->where('zencart_version_id', $this->zenCartVersion);
                    });
                }

                if ($this->isEncapsulated === 'yes') {
                    $versionQuery->where('is_encapsulated', true);
                } elseif ($this->isEncapsulated === 'no') {
                    $versionQuery->where('is_encapsulated', false);
                }

                if ($this->phpVersion) {
                    $versionQuery->where('php_version', 'like', $this->phpVersion.'%');
                }
            });
        }

        $filteredIds = $filterQuery->pluck('id');

        if ($filteredIds->isNotEmpty()) {
            $searchQuery->whereIn('id', $filteredIds);
        }

        return $searchQuery
            ->take($limit)
            ->get()
            ->map(function ($plugin) {
                return [
                    'type' => 'plugin',
                    'title' => $plugin->name,
                    'description' => $plugin->description,
                    'url' => route('plugins.show', $plugin->slug),
                    'meta' => $plugin->group?->name,
                    'icon' => 'heroicon-o-puzzle-piece',
                ];
            })
            ->toArray();
    }

    private function searchPluginVersions($query, $limit = 2)
    {
        return PluginVersion::search($query)
            ->take($limit)
            ->get()
            ->map(function ($version) {
                return [
                    'type' => 'plugin_version',
                    'title' => $version->plugin?->name.' v'.$version->version,
                    'description' => $version->description ?: $version->plugin?->description,
                    'url' => route('plugins.show', $version->plugin?->slug),
                    'meta' => 'Plugin Version',
                    'icon' => 'heroicon-o-tag',
                ];
            })
            ->toArray();
    }

    private function searchThreads($query, $limit = 3)
    {
        $searchQuery = Thread::search($query);

        // Get thread IDs that match our filters
        $threadQuery = Thread::query();

        // Apply forum group filter if set
        if ($this->forumGroup) {
            $forumIds = Forum::where('forum_group_id', $this->forumGroup)->pluck('id');
            $threadQuery->whereIn('forum_id', $forumIds);
        }

        // Apply date range filter
        if ($this->dateRange !== 'all') {
            $date = match ($this->dateRange) {
                'today' => now()->startOfDay(),
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => null,
            };

            if ($date) {
                $threadQuery->where('created_at', '>=', $date);
            }
        }

        // Apply author filter if set
        if ($this->author) {
            $threadQuery->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->author.'%');
            });
        }

        $filteredIds = $threadQuery->pluck('id');

        if ($filteredIds->isNotEmpty()) {
            $searchQuery->whereIn('id', $filteredIds);
        }

        return $searchQuery
            ->take($limit)
            ->get()
            ->map(function ($thread) {
                return [
                    'type' => 'thread',
                    'title' => $thread->title,
                    'description' => $thread->forum?->name,
                    'url' => route('forums.threads.show', [$thread->forum?->group?->slug, $thread->forum?->slug, $thread->slug]),
                    'meta' => $thread->posts()->count().' posts • '.$thread->views.' views',
                    'icon' => 'heroicon-o-chat-bubble-left-right',
                ];
            })
            ->toArray();
    }

    private function searchPosts($query, $limit = 2)
    {
        return Post::search($query)
            ->take($limit)
            ->get()
            ->map(function ($post) {
                return [
                    'type' => 'post',
                    'title' => 'Post in: '.$post->thread?->title,
                    'description' => \Str::limit(strip_tags($post->content), 100),
                    'url' => route('forums.threads.show', [$post->forum?->group?->slug, $post->forum?->slug, $post->thread?->slug]).'#post-'.$post->id,
                    'meta' => 'by '.$post->user?->name,
                    'icon' => 'heroicon-o-document-text',
                ];
            })
            ->toArray();
    }

    private function searchForums($query, $limit = 2)
    {
        return Forum::search($query)
            ->take($limit)
            ->get()
            ->map(function ($forum) {
                return [
                    'type' => 'forum',
                    'title' => $forum->name,
                    'description' => $forum->description,
                    'url' => route('forums.show', [$forum->group?->slug, $forum->slug]),
                    'meta' => $forum->group?->name,
                    'icon' => 'heroicon-o-rectangle-stack',
                ];
            })
            ->toArray();
    }

    private function searchForumGroups($query, $limit = 1)
    {
        return ForumGroup::search($query)
            ->take($limit)
            ->get()
            ->map(function ($group) {
                return [
                    'type' => 'forum_group',
                    'title' => $group->name,
                    'description' => $group->description,
                    'url' => route('forums.index').'#'.$group->slug,
                    'meta' => $group->forums()->count().' forums',
                    'icon' => 'heroicon-o-folder',
                ];
            })
            ->toArray();
    }

    private function searchPluginGroups($query, $limit = 1)
    {
        return PluginGroup::search($query)
            ->take($limit)
            ->get()
            ->map(function ($group) {
                return [
                    'type' => 'plugin_group',
                    'title' => $group->name,
                    'description' => $group->description,
                    'url' => route('plugins.group', $group->slug),
                    'meta' => $group->plugins()->count().' plugins',
                    'icon' => 'heroicon-o-folder-open',
                ];
            })
            ->toArray();
    }

    public function submitSearch()
    {
        if (strlen(trim($this->query)) < 1) {
            return;
        }

        $params = ['q' => trim($this->query)];

        // Add advanced search parameters if they're set
        if ($this->showAdvanced) {
            if ($this->searchIn !== 'all') {
                $params['type'] = $this->searchIn;
            }
            if ($this->pluginGroup) {
                $params['plugin_group'] = $this->pluginGroup;
            }
            if ($this->forumGroup) {
                $params['forum_group'] = $this->forumGroup;
            }
            if ($this->dateRange !== 'all') {
                $params['date'] = $this->dateRange;
            }
            if ($this->author) {
                $params['author'] = $this->author;
            }

            // Plugin-specific filters
            if ($this->zenCartVersion) {
                $params['zc_version'] = $this->zenCartVersion;
            }
            if ($this->pluginStatus !== 'all') {
                $params['status'] = $this->pluginStatus;
            }
            if ($this->isEncapsulated !== 'all') {
                $params['encapsulated'] = $this->isEncapsulated;
            }
            if ($this->phpVersion) {
                $params['php_version'] = $this->phpVersion;
            }
        }

        return redirect()->route('search', $params);
    }

    public function hideDropdown()
    {
        $this->showDropdown = false;
    }

    public function toggleAdvanced()
    {
        $this->showAdvanced = ! $this->showAdvanced;
    }

    public function getPluginGroups()
    {
        return PluginGroup::orderBy('name')->get();
    }

    public function getForumGroups()
    {
        return ForumGroup::orderBy('name')->get();
    }

    public function getZenCartVersions()
    {
        return ZencartVersion::orderBy('version', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.global-search', [
            'pluginGroups' => $this->currentContext === 'plugins' || $this->searchIn === 'plugins' ? $this->getPluginGroups() : collect(),
            'forumGroups' => $this->currentContext === 'forums' || $this->searchIn === 'forums' ? $this->getForumGroups() : collect(),
            'zenCartVersions' => $this->currentContext === 'plugins' || $this->searchIn === 'plugins' ? $this->getZenCartVersions() : collect(),
        ]);
    }
}
