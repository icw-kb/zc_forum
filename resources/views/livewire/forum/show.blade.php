<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('forums.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Forums
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ $forumGroup->name }}</span>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ $forum->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Forum Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $forum->name }}</h1>
        @if($forum->description)
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">{{ $forum->description }}</p>
        @endif
    </div>

    {{-- Sorting Controls --}}
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
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Thread Info --}}
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('forums.threads.show', [$forumGroup->slug, $forum->slug, $thread->slug]) }}" 
                                       class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 block truncate">
                                        {{ $thread->title }}
                                    </a>
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
</div>