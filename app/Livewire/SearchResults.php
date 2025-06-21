<?php

namespace App\Livewire;

use App\Models\Forum;
use App\Models\ForumGroup;
use App\Models\Plugin;
use App\Models\PluginGroup;
use App\Models\PluginVersion;
use App\Models\Post;
use App\Models\Thread;
use Livewire\Component;
use Livewire\WithPagination;

class SearchResults extends Component
{
    use WithPagination;

    public $query = '';
    public $activeFilter = 'all';
    public $sortBy = 'relevance';
    
    protected $queryString = [
        'query' => ['except' => '', 'as' => 'q'],
        'activeFilter' => ['except' => 'all', 'as' => 'filter'],
        'sortBy' => ['except' => 'relevance', 'as' => 'sort'],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        // Query parameter is handled by queryString configuration
    }

    public function updatingActiveFilter()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function getResultsProperty()
    {
        if (strlen(trim($this->query)) < 1) {
            return collect();
        }

        $results = collect();
        
        switch ($this->activeFilter) {
            case 'plugins':
                $results = $this->getPluginResults();
                break;
            case 'plugin_versions':
                $results = $this->getPluginVersionResults();
                break;
            case 'threads':
                $results = $this->getThreadResults();
                break;
            case 'posts':
                $results = $this->getPostResults();
                break;
            case 'forums':
                $results = $this->getForumResults();
                break;
            case 'categories':
                $results = $this->getCategoryResults();
                break;
            default:
                $results = $this->getAllResults();
                break;
        }

        return $results;
    }

    private function getPluginResults()
    {
        return Plugin::search($this->query)
            ->paginate(15)
            ->through(function ($plugin) {
                return [
                    'type' => 'plugin',
                    'title' => $plugin->name,
                    'description' => $plugin->description,
                    'url' => route('plugins.show', $plugin->slug),
                    'meta' => [
                        'group' => $plugin->group?->name,
                        'downloads' => number_format($plugin->download_count),
                        'views' => number_format($plugin->view_count),
                        'featured' => $plugin->is_featured,
                    ],
                    'created_at' => $plugin->created_at,
                    'updated_at' => $plugin->updated_at,
                ];
            });
    }

    private function getPluginVersionResults()
    {
        return PluginVersion::search($this->query)
            ->paginate(15)
            ->through(function ($version) {
                return [
                    'type' => 'plugin_version',
                    'title' => $version->plugin?->name . ' v' . $version->version,
                    'description' => $version->description ?: $version->plugin?->description,
                    'url' => route('plugins.show', $version->plugin?->slug),
                    'meta' => [
                        'group' => $version->plugin?->group?->name,
                        'php_version' => $version->php_version,
                        'zencart_versions' => $version->zencartVersions->pluck('version')->implode(', '),
                        'file_size' => $version->formatted_file_size,
                    ],
                    'created_at' => $version->created_at,
                    'updated_at' => $version->updated_at,
                ];
            });
    }

    private function getThreadResults()
    {
        return Thread::search($this->query)
            ->paginate(15)
            ->through(function ($thread) {
                return [
                    'type' => 'thread',
                    'title' => $thread->title,
                    'description' => \Str::limit(strip_tags($thread->content ?? ''), 200),
                    'url' => route('forums.threads.show', [$thread->forum?->group?->slug, $thread->forum?->slug, $thread->slug]),
                    'meta' => [
                        'forum' => $thread->forum?->name,
                        'user' => $thread->user?->name,
                        'posts' => $thread->posts()->count(),
                        'views' => number_format($thread->views ?? 0),
                        'pinned' => $thread->pinned,
                    ],
                    'created_at' => $thread->created_at,
                    'updated_at' => $thread->updated_at,
                ];
            });
    }

    private function getPostResults()
    {
        return Post::search($this->query)
            ->paginate(15)
            ->through(function ($post) {
                return [
                    'type' => 'post',
                    'title' => 'Post in: ' . $post->thread?->title,
                    'description' => \Str::limit(strip_tags($post->content), 300),
                    'url' => route('forums.threads.show', [$post->forum?->group?->slug, $post->forum?->slug, $post->thread?->slug]) . '#post-' . $post->id,
                    'meta' => [
                        'thread' => $post->thread?->title,
                        'forum' => $post->forum?->name,
                        'user' => $post->user?->name,
                        'likes' => $post->likes()->count(),
                        'accepted' => $post->is_accepted_answer ?? false,
                    ],
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                ];
            });
    }

    private function getForumResults()
    {
        return Forum::search($this->query)
            ->paginate(15)
            ->through(function ($forum) {
                return [
                    'type' => 'forum',
                    'title' => $forum->name,
                    'description' => $forum->description,
                    'url' => route('forums.show', [$forum->group?->slug, $forum->slug]),
                    'meta' => [
                        'group' => $forum->group?->name,
                        'threads' => $forum->threads()->count(),
                        'posts' => $forum->posts()->count(),
                    ],
                    'created_at' => $forum->created_at,
                    'updated_at' => $forum->updated_at,
                ];
            });
    }

    private function getCategoryResults()
    {
        $forumGroups = ForumGroup::search($this->query)->take(5)->get();
        $pluginGroups = PluginGroup::search($this->query)->take(5)->get();
        
        $results = collect();
        
        foreach ($forumGroups as $group) {
            $results->push([
                'type' => 'forum_group',
                'title' => $group->name,
                'description' => $group->description,
                'url' => route('forums.index') . '#' . $group->slug,
                'meta' => [
                    'forums' => $group->forums()->count(),
                    'type' => 'Forum Category',
                ],
                'created_at' => $group->created_at,
                'updated_at' => $group->updated_at,
            ]);
        }
        
        foreach ($pluginGroups as $group) {
            $results->push([
                'type' => 'plugin_group',
                'title' => $group->name,
                'description' => $group->description,
                'url' => route('plugins.group', $group->slug),
                'meta' => [
                    'plugins' => $group->plugins()->count(),
                    'type' => 'Plugin Category',
                ],
                'created_at' => $group->created_at,
                'updated_at' => $group->updated_at,
            ]);
        }
        
        return $results->sortBy('title');
    }

    private function getAllResults()
    {
        $results = collect();
        
        // Get limited results from each type
        $plugins = Plugin::search($this->query)->take(5)->get();
        $pluginVersions = PluginVersion::search($this->query)->take(3)->get();
        $threads = Thread::search($this->query)->take(5)->get();
        $posts = Post::search($this->query)->take(3)->get();
        $forums = Forum::search($this->query)->take(3)->get();
        $forumGroups = ForumGroup::search($this->query)->take(2)->get();
        $pluginGroups = PluginGroup::search($this->query)->take(2)->get();
        
        // Transform and merge all results
        foreach ($plugins as $plugin) {
            $results->push([
                'type' => 'plugin',
                'title' => $plugin->name,
                'description' => $plugin->description,
                'url' => route('plugins.show', $plugin->slug),
                'meta' => [
                    'group' => $plugin->group?->name,
                    'downloads' => number_format($plugin->download_count),
                    'featured' => $plugin->is_featured,
                ],
                'created_at' => $plugin->created_at,
                'relevance_score' => 100, // Plugins get higher relevance
            ]);
        }
        
        foreach ($threads as $thread) {
            $results->push([
                'type' => 'thread',
                'title' => $thread->title,
                'description' => \Str::limit(strip_tags($thread->content ?? ''), 200),
                'url' => route('forums.threads.show', [$thread->forum?->group?->slug, $thread->forum?->slug, $thread->slug]),
                'meta' => [
                    'forum' => $thread->forum?->name,
                    'posts' => $thread->posts()->count(),
                    'views' => number_format($thread->views ?? 0),
                ],
                'created_at' => $thread->created_at,
                'relevance_score' => 90,
            ]);
        }
        
        foreach ($forums as $forum) {
            $results->push([
                'type' => 'forum',
                'title' => $forum->name,
                'description' => $forum->description,
                'url' => route('forums.show', [$forum->group?->slug, $forum->slug]),
                'meta' => [
                    'group' => $forum->group?->name,
                    'threads' => $forum->threads()->count(),
                ],
                'created_at' => $forum->created_at,
                'relevance_score' => 80,
            ]);
        }
        
        foreach ($posts as $post) {
            $results->push([
                'type' => 'post',
                'title' => 'Post in: ' . $post->thread?->title,
                'description' => \Str::limit(strip_tags($post->content), 200),
                'url' => route('forums.threads.show', [$post->forum?->group?->slug, $post->forum?->slug, $post->thread?->slug]) . '#post-' . $post->id,
                'meta' => [
                    'thread' => $post->thread?->title,
                    'user' => $post->user?->name,
                    'likes' => $post->likes()->count(),
                    'accepted' => $post->is_accepted_answer ?? false,
                ],
                'created_at' => $post->created_at,
                'relevance_score' => 70,
            ]);
        }
        
        foreach ($pluginVersions as $version) {
            $results->push([
                'type' => 'plugin_version',
                'title' => $version->plugin?->name . ' v' . $version->version,
                'description' => $version->description ?: $version->plugin?->description,
                'url' => route('plugins.show', $version->plugin?->slug),
                'meta' => [
                    'group' => $version->plugin?->group?->name,
                    'php_version' => $version->php_version,
                    'file_size' => $version->formatted_file_size,
                ],
                'created_at' => $version->created_at,
                'relevance_score' => 60,
            ]);
        }
        
        foreach ($forumGroups as $group) {
            $results->push([
                'type' => 'forum_group',
                'title' => $group->name,
                'description' => $group->description,
                'url' => route('forums.index') . '#' . $group->slug,
                'meta' => [
                    'forums' => $group->forums()->count(),
                    'type' => 'Forum Category',
                ],
                'created_at' => $group->created_at,
                'relevance_score' => 50,
            ]);
        }
        
        foreach ($pluginGroups as $group) {
            $results->push([
                'type' => 'plugin_group',
                'title' => $group->name,
                'description' => $group->description,
                'url' => route('plugins.group', $group->slug),
                'meta' => [
                    'plugins' => $group->plugins()->count(),
                    'type' => 'Plugin Category',
                ],
                'created_at' => $group->created_at,
                'relevance_score' => 50,
            ]);
        }
        
        return $results->sortByDesc('relevance_score')->take(20);
    }

    public function render()
    {
        return view('livewire.search-results', [
            'results' => $this->results,
        ])->layout('layouts.app')->title('Search Results');
    }
}