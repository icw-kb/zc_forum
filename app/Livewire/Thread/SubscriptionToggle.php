<?php

namespace App\Livewire\Thread;

use App\Models\Thread;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionToggle extends Component
{
    public Thread $thread;

    public bool $isSubscribed = false;

    public function mount(Thread $thread)
    {
        $this->thread = $thread;
        $this->checkSubscriptionStatus();
    }

    public function checkSubscriptionStatus()
    {
        // TEMPORARY: Auth check disabled for testing
        // if (Auth::check()) {
        //     $this->isSubscribed = $this->thread->isSubscribedBy(Auth::user());
        // }

        // For testing purposes, assume user ID 1
        $user = Auth::user() ?? \App\Models\User::find(1);
        if ($user) {
            $this->isSubscribed = $this->thread->isSubscribedBy($user);
        }
    }

    public function toggleSubscription()
    {
        // TEMPORARY: Auth check disabled for testing
        // if (!Auth::check()) {
        //     $this->dispatch('open-login-modal');
        //     return;
        // }

        // For testing purposes, use user ID 1
        $user = Auth::user() ?? \App\Models\User::find(1);
        if (! $user) {
            return;
        }

        if ($this->isSubscribed) {
            $this->thread->unsubscribe($user);
            $this->isSubscribed = false;
            $this->dispatch('thread-unsubscribed');
        } else {
            $this->thread->subscribe($user);
            $this->isSubscribed = true;
            $this->dispatch('thread-subscribed');
        }
    }

    public function render()
    {
        return view('livewire.thread.subscription-toggle');
    }
}
