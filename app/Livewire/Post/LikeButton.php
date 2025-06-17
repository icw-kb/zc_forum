<?php

namespace App\Livewire\Post;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LikeButton extends Component
{
    public Post $post;

    public bool $isLiked = false;

    public int $likesCount = 0;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->checkLikeStatus();
        $this->updateLikesCount();
    }

    public function checkLikeStatus()
    {
        // TEMPORARY: Auth check disabled for testing
        // For testing purposes, use user ID 1
        $user = Auth::user() ?? \App\Models\User::find(1);
        if ($user) {
            $this->isLiked = $this->post->isLikedBy($user);
        }
    }

    public function updateLikesCount()
    {
        $this->likesCount = $this->post->likes()->count();
    }

    public function toggleLike()
    {
        // TEMPORARY: Auth check disabled for testing
        // For testing purposes, use user ID 1
        $user = Auth::user() ?? \App\Models\User::find(1);
        if (! $user) {
            return;
        }

        if ($this->isLiked) {
            $this->post->unlike($user);
            $this->isLiked = false;
            $this->dispatch('post-unliked');
        } else {
            $this->post->like($user);
            $this->isLiked = true;
            $this->dispatch('post-liked');
        }

        $this->updateLikesCount();
    }

    public function render()
    {
        return view('livewire.post.like-button');
    }
}
