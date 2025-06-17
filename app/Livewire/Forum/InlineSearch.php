<?php

namespace App\Livewire\Forum;

use App\Models\Forum;
use App\Models\Post;
use App\Models\Thread;
use Livewire\Component;

class InlineSearch extends Component
{
    public string $query = '';
    public string $searchType = 'all'; // all, threads, posts
    public ?Forum $forum = null;
    public bool $showResults = false;

    public function mount(?Forum $forum = null)
    {
        $this->forum = $forum;
    }

    public function updatedQuery()
    {
        $this->showResults = !empty($this->query);
    }

    public function updatedSearchType()
    {
        // Trigger search when type changes
        $this->showResults = !empty($this->query);
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->showResults = false;
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
        })->take(10)->values();
    }

    private function searchThreads()
    {
        $query = Thread::query()
            ->with(['user', 'forum.forumGroup', 'posts' => function ($q) {
                $q->oldest()->limit(1);
            }])
            ->where('title', 'like', '%'.$this->query.'%');

        if ($this->forum) {
            $query->where('forum_id', $this->forum->id);
        }

        return $query->latest()->limit(5)->get();
    }

    private function searchPosts()
    {
        $query = Post::query()
            ->with(['user', 'thread.forum.forumGroup'])
            ->where('content', 'like', '%'.$this->query.'%');

        if ($this->forum) {
            $query->whereHas('thread', function ($q) {
                $q->where('forum_id', $this->forum->id);
            });
        }

        return $query->latest()->limit(5)->get();
    }

    public function render()
    {
        return view('livewire.forum.inline-search', [
            'searchResults' => $this->getSearchResults(),
        ]);
    }
}