<?php

namespace App\Livewire\Forum;

use App\Models\ForumGroup;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        // Get all forum groups with their forums, ordered by sort_order
        $forumGroups = ForumGroup::with(['forums' => function ($query) {
            $query->orderBy('sort_order')
                ->withCount(['threads', 'posts'])
                ->with(['latestPost.user', 'latestPost.thread']);
        }])
            ->orderBy('sort_order')
            ->get();

        return view('livewire.forum.index', [
            'forumGroups' => $forumGroups,
        ])->layout('layouts.app', ['title' => 'Forums']);
    }
}
