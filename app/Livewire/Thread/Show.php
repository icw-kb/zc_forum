<?php

namespace App\Livewire\Thread;

use App\Models\Forum;
use App\Models\ForumGroup;
use App\Models\Post;
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

    protected $listeners = ['refresh-posts' => '$refresh'];

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

    public function quotePost($postId)
    {
        $post = Post::with('user')->findOrFail($postId);
        
        // Create quote text with author attribution
        $quoteText = "[quote={$post->user->name}]\n{$post->content}\n[/quote]\n\n";
        
        // Dispatch event to the Post Create component to open form with quote
        $this->dispatch('quote-post', quotedContent: $quoteText);
    }

    public function formatPostContent($content)
    {
        // First escape the content for safety
        $escapedContent = e($content);
        
        // Convert [quote=username]content[/quote] to HTML
        $pattern = '/\[quote=([^\]]+)\](.*?)\[\/quote\]/s';
        $replacement = '<div class="quote-block bg-gray-100 dark:bg-gray-700 border-l-4 border-blue-500 dark:border-blue-400 p-4 my-4 rounded-r-lg">
            <div class="quote-header text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                $1 said:
            </div>
            <div class="quote-content text-gray-700 dark:text-gray-300 italic">$2</div>
        </div>';
        
        $formatted = preg_replace($pattern, $replacement, $escapedContent);
        
        return nl2br($formatted);
    }

    public function render()
    {
        $posts = $this->thread->posts()
            ->with(['user'])
            ->oldest()
            ->paginate($this->perPage);

        // TEMPORARY: Mark posts as read for testing (using user ID 1)
        $user = \Illuminate\Support\Facades\Auth::user() ?? \App\Models\User::find(1);
        if ($user) {
            foreach ($posts as $post) {
                if ($post->isNewFor($user)) {
                    $post->markAsRead($user);
                }
            }
        }

        return view('livewire.thread.show', [
            'posts' => $posts,
        ])->layout('layouts.app', [
            'title' => $this->thread->title.' - '.$this->forum->name,
        ]);
    }
}
