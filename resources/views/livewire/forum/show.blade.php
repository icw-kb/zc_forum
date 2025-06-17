<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <x-forum-breadcrumb :items="[
        ['title' => $forumGroup->name],
        ['title' => $forum->name]
    ]" />

    {{-- Forum Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $forum->name }}</h1>
        @if($forum->description)
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">{{ $forum->description }}</p>
        @endif
    </div>

    {{-- Sorting Controls & Create Thread Button --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</label>
            <select wire:model.live="sortBy" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="latest">Latest Activity</option>
                <option value="replies">Most Replies</option>
                <option value="views">Most Views</option>
                <option value="oldest">Oldest First</option>
            </select>
        </div>
        
        {{-- TEMPORARY: Permissions disabled for testing --}}
        {{-- @can('create', [App\Models\Thread::class, $forum]) --}}
            <button 
                wire:click="$dispatch('open-thread-create-modal')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Thread
            </button>
        {{-- @endcan --}}
    </div>

    {{-- Threads List --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @if($threads->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-600">
                @foreach($threads as $thread)
                    <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1 min-w-0">
                                {{-- Thread Icon --}}
                                <div class="flex-shrink-0 mt-1">
                                    @php
                                        // TEMPORARY: Check if thread has unread posts (using user ID 1 for testing)
                                        $user = auth()->user() ?? \App\Models\User::find(1);
                                        $hasUnread = $user ? $thread->hasUnreadPostsFor($user) : true;
                                    @endphp
                                    @php
                                        $iconColor = 'bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400';
                                        $iconSvg = 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z';
                                        
                                        if ($thread->isPinned()) {
                                            $iconColor = 'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400';
                                            $iconSvg = 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z';
                                        } elseif ($thread->isLocked()) {
                                            $iconColor = 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400';
                                            $iconSvg = 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z';
                                        } elseif ($hasUnread) {
                                            $iconColor = 'bg-blue-600 dark:bg-blue-500 text-white';
                                        }
                                    @endphp
                                    <div class="w-8 h-8 {{ $iconColor }} rounded-full flex items-center justify-center relative">
                                        <svg class="w-4 h-4" fill="{{ $thread->isPinned() ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconSvg }}"/>
                                        </svg>
                                        @if($hasUnread && !$thread->isPinned() && !$thread->isLocked())
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Thread Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('forums.threads.show', [$forumGroup->slug, $forum->slug, $thread->slug]) }}" 
                                           class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                            {{ $thread->title }}
                                        </a>
                                        @if($thread->isPinned())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Pinned
                                            </span>
                                        @endif
                                        @if($thread->isLocked())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Locked
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <span>by {{ $thread->user->name }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $thread->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Thread Stats --}}
                            <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                <div class="text-center">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $thread->posts_count ?? 0 }}</div>
                                    <div>Replies</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $thread->views ?? 0 }}</div>
                                    <div>Views</div>
                                </div>
                                @if($thread->latestPost)
                                    <div class="text-center min-w-0">
                                        <div class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $thread->latestPost->user->name }}
                                        </div>
                                        <div class="truncate">{{ $thread->latestPost->created_at->diffForHumans() }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                {{ $threads->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No threads yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Be the first to start a discussion in this forum.</p>
            </div>
        @endif
    </div>

    {{-- Thread Creation Modal --}}
    @livewire('thread.create', ['forumGroup' => $forumGroup, 'forum' => $forum])
</div>