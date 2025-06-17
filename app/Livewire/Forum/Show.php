<?php

namespace App\Livewire\Forum;

use App\Models\Forum;
use App\Models\ForumGroup;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public ForumGroup $forumGroup;

    public Forum $forum;

    public $sortBy = 'latest';

    public $perPage = 20;

    protected $queryString = [
        'sortBy' => ['except' => 'latest'],
    ];

    public function mount(ForumGroup $forumGroup, Forum $forum)
    {
        $this->forumGroup = $forumGroup;
        $this->forum = $forum;

        // Authorize forum access
        $this->authorize('view', $this->forum);
    }

    public function updating($property)
    {
        if ($property === 'sortBy') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = $this->forum->threads()
            ->with(['user', 'forum'])
            ->withCount(['posts'])
            ->with(['latestPost.user']);

        // Apply sorting
        switch ($this->sortBy) {
            case 'replies':
                $query->orderBy('posts_count', 'desc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest('updated_at');
        }

        $threads = $query->paginate($this->perPage);

        return view('livewire.forum.show', [
            'threads' => $threads,
        ])->layout('layouts.app', [
            'title' => $this->forum->name.' - '.$this->forumGroup->name,
        ]);
    }
}
