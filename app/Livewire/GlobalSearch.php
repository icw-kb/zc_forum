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

class GlobalSearch extends Component
{
    public $query = '';
    public $showDropdown = false;
    public $results = [];
    public $isLoading = false;

    protected $listeners = ['hideSearchDropdown'];

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

    private function searchPlugins($query, $limit = 3)
    {
        return Plugin::search($query)
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
                    'title' => $version->plugin?->name . ' v' . $version->version,
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
        return Thread::search($query)
            ->take($limit)
            ->get()
            ->map(function ($thread) {
                return [
                    'type' => 'thread',
                    'title' => $thread->title,
                    'description' => $thread->forum?->name,
                    'url' => route('forums.threads.show', [$thread->forum?->group?->slug, $thread->forum?->slug, $thread->slug]),
                    'meta' => $thread->posts()->count() . ' posts • ' . $thread->views . ' views',
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
                    'title' => 'Post in: ' . $post->thread?->title,
                    'description' => \Str::limit(strip_tags($post->content), 100),
                    'url' => route('forums.threads.show', [$post->forum?->group?->slug, $post->forum?->slug, $post->thread?->slug]) . '#post-' . $post->id,
                    'meta' => 'by ' . $post->user?->name,
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
                    'url' => route('forums.index') . '#' . $group->slug,
                    'meta' => $group->forums()->count() . ' forums',
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
                    'meta' => $group->plugins()->count() . ' plugins',
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

        return redirect()->route('search', ['q' => trim($this->query)]);
    }

    public function hideDropdown()
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}