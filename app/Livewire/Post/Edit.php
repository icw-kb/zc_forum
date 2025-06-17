<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public Post $post;

    public string $content = '';

    public bool $showForm = false;

    protected $rules = [
        'content' => ['required', 'string', 'min:10'],
    ];

    protected $listeners = ['open-post-edit' => 'openEdit'];

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->content = $this->post->content;
    }

    public function openEdit($postId)
    {
        // Only open if this is the correct post
        if ($this->post->id !== $postId) {
            return;
        }

        // TEMPORARY: Auth check disabled for testing
        // Check if user is authenticated
        // if (! Auth::check()) {
        //     $this->dispatch('open-login-modal');
        //     return;
        // }

        // TEMPORARY: Permissions disabled for testing
        // $this->authorize('update', $this->post);

        $this->resetErrorBag();
        $this->content = $this->post->content;
        $this->showForm = true;
    }

    public function updatePost()
    {
        // TEMPORARY: Auth check disabled for testing
        // Ensure user is authenticated
        // if (! Auth::check()) {
        //     $this->dispatch('open-login-modal');
        //     return;
        // }

        // TEMPORARY: Permissions disabled for testing
        // $this->authorize('update', $this->post);

        $this->validate();

        // Update the post
        $this->post->update([
            'content' => $this->content,
        ]);

        $this->showForm = false;

        // Show success message
        $this->dispatch('post-updated');

        // Refresh the parent component
        $this->dispatch('refresh-posts');
    }

    public function cancelEdit()
    {
        $this->showForm = false;
        $this->resetErrorBag();
        $this->content = $this->post->content;
    }

    public function render()
    {
        return view('livewire.post.edit');
    }
}
