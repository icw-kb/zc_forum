<?php

namespace App\Livewire\Forum;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use Livewire\Component;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    public string $query = '';

    public string $searchType = 'all'; // all, threads, posts

    public ?Forum $forum = null;

    public int $perPage = 20;

    protected $queryString = [
        'query' => ['except' => ''],
        'searchType' => ['except' => 'all'],
    ];

    public function mount(?Forum $forum = null)
    {
        $this->forum = $forum;
    }

    public function updating($property)
    {
        if (in_array($property, ['query', 'searchType'])) {
            $this->resetPage();
        }
    }

    public function search()
    {
        $this->resetPage();
    }

    public function getSearchResults()
    {
        if (empty($this->query)) {
            return collect();
        }

        $results = collect();

        if ($this->searchType === 'all' || $this->searchType === 'threads') {
            $threads = $this->searchThreads();
            $results = $results->merge($threads->map(function ($thread) {
                return [
                    'type' => 'thread',
                    'item' => $thread,
                    'excerpt' => \Str::limit(strip_tags($thread->posts->first()?->content ?? ''), 150),
                ];
            }));
        }

        if ($this->searchType === 'all' || $this->searchType === 'posts') {
            $posts = $this->searchPosts();
            $results = $results->merge($posts->map(function ($post) {
                return [
                    'type' => 'post',
                    'item' => $post,
                    'excerpt' => \Str::limit(strip_tags($post->content), 150),
                ];
            }));
        }

        return $results->sortByDesc(function ($item) {
            return $item['item']->updated_at ?? $item['item']->created_at;
        })->values();
    }

    private function searchThreads()
    {
        $query = Thread::query()
            ->with(['user', 'forum', 'posts' => function ($q) {
                $q->oldest()->limit(1);
            }])
            ->where('title', 'like', '%'.$this->query.'%');

        if ($this->forum) {
            $query->where('forum_id', $this->forum->id);
        }

        return $query->latest()->limit(10)->get();
    }

    private function searchPosts()
    {
        $query = Post::query()
            ->with(['user', 'thread.forum'])
            ->where('content', 'like', '%'.$this->query.'%');

        if ($this->forum) {
            $query->whereHas('thread', function ($q) {
                $q->where('forum_id', $this->forum->id);
            });
        }

        return $query->latest()->limit(10)->get();
    }

    public function render()
    {
        $results = $this->getSearchResults();

        return view('livewire.forum.search', [
            'results' => $results,
        ])->layout('layouts.app', [
            'title' => 'Search Forums'.($this->forum ? ' - '.$this->forum->name : ''),
        ]);
    }
}
