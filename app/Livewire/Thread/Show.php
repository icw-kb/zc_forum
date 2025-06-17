<?php

namespace App\Livewire\Thread;

use App\Models\Forum;
use App\Models\ForumGroup;
use App\Models\Thread;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public ForumGroup $forumGroup;

    public Forum $forum;

    public Thread $thread;

    public $perPage = 15;

    public function mount(ForumGroup $forumGroup, Forum $forum, Thread $thread)
    {
        $this->forumGroup = $forumGroup;
        $this->forum = $forum;
        $this->thread = $thread;

        // Authorize thread access
        $this->authorize('view', $this->forum);

        // Increment views count
        $this->thread->increment('views');
    }

    public function render()
    {
        $posts = $this->thread->posts()
            ->with(['user'])
            ->oldest()
            ->paginate($this->perPage);

        return view('livewire.thread.show', [
            'posts' => $posts,
        ])->layout('layouts.app', [
            'title' => $this->thread->title.' - '.$this->forum->name,
        ]);
    }
}
