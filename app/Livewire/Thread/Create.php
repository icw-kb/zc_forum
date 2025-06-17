<?php

namespace App\Livewire\Thread;

use App\Models\Forum;
use App\Models\ForumGroup;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public bool $open = false;

    public ForumGroup $forumGroup;

    public Forum $forum;

    public string $title = '';

    public string $content = '';

    protected $rules = [
        'title' => ['required', 'string', 'min:3', 'max:255'],
        'content' => ['required', 'string', 'min:10'],
    ];

    protected $listeners = ['open-thread-create-modal' => 'openModal'];

    public function mount(ForumGroup $forumGroup, Forum $forum)
    {
        $this->forumGroup = $forumGroup;
        $this->forum = $forum;
    }

    public function openModal(): void
    {
        // TEMPORARY: Auth check disabled for testing
        // Check if user is authenticated
        // if (! Auth::check()) {
        //     $this->dispatch('open-login-modal');
        //     return;
        // }

        // TEMPORARY: Permissions disabled for testing
        // $this->authorize('create', [Thread::class, $this->forum]);

        $this->resetErrorBag();
        $this->reset(['title', 'content']);
        $this->open = true;
    }

    public function createThread()
    {
        // TEMPORARY: Auth check disabled for testing
        // Ensure user is authenticated
        // if (! Auth::check()) {
        //     $this->dispatch('open-login-modal');
        //     return;
        // }

        // TEMPORARY: Permissions disabled for testing
        // $this->authorize('create', [Thread::class, $this->forum]);

        $this->validate();

        // Create the thread
        // TEMPORARY: Using a default user_id for testing (1 = admin)
        $thread = Thread::create([
            'title' => $this->title,
            'forum_id' => $this->forum->id,
            'user_id' => Auth::id() ?? 1, // Default to user 1 for guest testing
        ]);

        // Create the first post for the thread
        Post::create([
            'content' => $this->content,
            'thread_id' => $thread->id,
            'user_id' => Auth::id() ?? 1, // Default to user 1 for guest testing
        ]);

        $this->open = false;

        // Redirect to the new thread
        return redirect()->route('forums.threads.show', [
            $this->forumGroup->slug,
            $this->forum->slug,
            $thread->slug,
        ]);
    }

    public function render()
    {
        return view('livewire.thread.create');
    }
}
