<?php

namespace App\Livewire\Post;

use App\Models\Forum;
use App\Models\ForumGroup;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public ForumGroup $forumGroup;

    public Forum $forum;

    public Thread $thread;

    public string $content = '';

    public bool $showForm = false;

    protected $rules = [
        'content' => ['required', 'string', 'min:10'],
    ];

    public function mount(ForumGroup $forumGroup, Forum $forum, Thread $thread)
    {
        $this->forumGroup = $forumGroup;
        $this->forum = $forum;
        $this->thread = $thread;
    }

    public function toggleForm()
    {
        // TEMPORARY: Auth check disabled for testing
        // Check if user is authenticated
        // if (! Auth::check()) {
        //     $this->dispatch('open-login-modal');
        //     return;
        // }

        // TEMPORARY: Permissions disabled for testing
        // $this->authorize('create', [Post::class, $this->forum]);

        $this->showForm = ! $this->showForm;
        $this->resetErrorBag();
        $this->reset('content');
    }

    public function createPost()
    {
        // TEMPORARY: Auth check disabled for testing
        // Ensure user is authenticated
        // if (! Auth::check()) {
        //     $this->dispatch('open-login-modal');
        //     return;
        // }

        // TEMPORARY: Permissions disabled for testing
        // $this->authorize('create', [Post::class, $this->forum]);

        $this->validate();

        // Create the post
        // TEMPORARY: Using a default user_id for testing (1 = admin)
        Post::create([
            'content' => $this->content,
            'thread_id' => $this->thread->id,
            'user_id' => Auth::id() ?? 1, // Default to user 1 for guest testing
        ]);

        // Update thread's updated_at timestamp
        $this->thread->touch();

        // Reset form
        $this->reset('content');
        $this->showForm = false;

        // Show success message
        $this->dispatch('post-created');

        // Refresh the parent component
        $this->dispatch('refresh-posts');
    }

    public function render()
    {
        return view('livewire.post.create');
    }
}
