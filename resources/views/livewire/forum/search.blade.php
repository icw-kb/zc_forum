<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Search Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            Search Forums
            @if($forum)
                - {{ $forum->name }}
            @endif
        </h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">Find threads and posts across the forum</p>
    </div>

    {{-- Search Form --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <form wire:submit.prevent="search" class="space-y-4">
            <div class="flex space-x-4">
                <div class="flex-1">
                    <label for="query" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search Terms
                    </label>
                    <input 
                        wire:model.live.debounce.300ms="query"
                        type="text" 
                        id="query" 
                        placeholder="Enter keywords to search..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                    />
                </div>
                <div class="w-40">
                    <label for="searchType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search In
                    </label>
                    <select 
                        wire:model.live="searchType"
                        id="searchType"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                    >
                        <option value="all">All</option>
                        <option value="threads">Threads</option>
                        <option value="posts">Posts</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Search Results --}}
    @if($query)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            @if($results->count() > 0)
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Search Results for "{{ $query }}" ({{ $results->count() }} found)
                    </h3>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($results as $result)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @if($result['type'] === 'thread')
                                {{-- Thread Result --}}
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                Thread
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                in {{ $result['item']->forum->name }}
                                            </span>
                                        </div>
                                        <a href="{{ route('forums.threads.show', [$result['item']->forum->forumGroup->slug, $result['item']->forum->slug, $result['item']->slug]) }}" 
                                           class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 block">
                                            {{ $result['item']->title }}
                                        </a>
                                        @if($result['excerpt'])
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $result['excerpt'] }}</p>
                                        @endif
                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            Started by {{ $result['item']->user->name }} • {{ $result['item']->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Post Result --}}
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Post
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                in {{ $result['item']->thread->forum->name }}
                                            </span>
                                        </div>
                                        <a href="{{ route('forums.threads.show', [$result['item']->thread->forum->forumGroup->slug, $result['item']->thread->forum->slug, $result['item']->thread->slug]) }}" 
                                           class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 block">
                                            Re: {{ $result['item']->thread->title }}
                                        </a>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $result['excerpt'] }}</p>
                                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            Posted by {{ $result['item']->user->name }} • {{ $result['item']->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No results found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search terms or search type.</p>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Start searching</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter keywords above to search through forums, threads, and posts.</p>
        </div>
    @endif
</div>