<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <x-forum-breadcrumb :items="[
        ['title' => $forumGroup->name],
        ['title' => $forum->name, 'url' => route('forums.show', [$forumGroup->slug, $forum->slug])],
        ['title' => Str::limit($thread->title, 30)]
    ]" />

    {{-- Thread Header --}}
    <div class="mb-8">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $thread->title }}</h1>
                <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <span>Started by {{ $thread->user->name }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $thread->created_at->diffForHumans() }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $thread->views }} {{ Str::plural('view', $thread->views) }}</span>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @livewire('thread.subscription-toggle', ['thread' => $thread])
            </div>
        </div>
    </div>

    {{-- Posts --}}
    <div class="space-y-6">
        @foreach($posts as $post)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="flex">
                    {{-- User Info Sidebar --}}
                    <div class="w-48 bg-gray-50 dark:bg-gray-700 p-6 border-r border-gray-200 dark:border-gray-600">
                        <div class="text-center">
                            {{-- Avatar --}}
                            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3">
                                <span class="text-xl font-medium text-blue-600 dark:text-blue-400">
                                    {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                </span>
                            </div>
                            
                            {{-- Username --}}
                            <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ $post->user->name }}</h3>
                            
                            {{-- User Stats --}}
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <div>Member since</div>
                                <div>{{ $post->user->created_at->format('M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Post Content --}}
                    <div class="flex-1 p-6">
                        {{-- Post Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <time datetime="{{ $post->created_at->toISOString() }}">
                                    {{ $post->created_at->format('M j, Y \a\t g:i A') }}
                                </time>
                            </div>
                            <div class="flex items-center space-x-2">
                                {{-- Post Actions --}}
                                {{-- TEMPORARY: Permissions disabled for testing --}}
                                {{-- @can('update', $post) --}}
                                    <button 
                                        wire:click="$dispatch('open-post-edit', [{{ $post->id }}])"
                                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                    >
                                        Edit
                                    </button>
                                {{-- @endcan --}}
                                <button class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    Quote
                                </button>
                                @livewire('post.like-button', ['post' => $post], key('post-like-' . $post->id))
                            </div>
                        </div>

                        {{-- Post Body --}}
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($post->content)) !!}
                        </div>

                        {{-- Post Edit Form --}}
                        @livewire('post.edit', ['post' => $post], key('post-edit-' . $post->id))

                        {{-- Post Footer --}}
                        @if($post->updated_at->gt($post->created_at))
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Last edited {{ $post->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($posts->hasPages())
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    @endif

    {{-- Empty State --}}
    @if($posts->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No posts yet</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This thread doesn't have any posts yet.</p>
        </div>
    @endif

    {{-- Reply Form --}}
    <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        @livewire('post.create', ['forumGroup' => $forumGroup, 'forum' => $forum, 'thread' => $thread])
    </div>
</div>