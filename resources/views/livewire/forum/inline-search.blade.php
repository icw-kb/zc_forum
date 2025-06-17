<div class="relative">
    {{-- Search Input --}}
    <div class="relative">
        <div class="flex space-x-2">
            <div class="flex-1 relative">
                <input 
                    wire:model.live.debounce.300ms="query"
                    type="text" 
                    placeholder="Search {{ $forum ? $forum->name : 'forums' }}..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 text-sm"
                />
                <div class="absolute left-3 top-2.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                @if($query)
                    <button 
                        wire:click="clearSearch"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>
            
            <select 
                wire:model.live="searchType"
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 text-sm"
            >
                <option value="all">All</option>
                <option value="threads">Threads</option>
                <option value="posts">Posts</option>
            </select>
        </div>
    </div>

    {{-- Search Results Dropdown --}}
    @if($showResults && $query)
        <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50 max-h-96 overflow-y-auto">
            @if($searchResults->count() > 0)
                <div class="p-3 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            Search Results ({{ $searchResults->count() }})
                        </h4>
                        <a href="{{ route('forums.search', ['query' => $query, 'searchType' => $searchType]) }}" 
                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                            View All
                        </a>
                    </div>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($searchResults as $result)
                        <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @if($result['type'] === 'thread')
                                {{-- Thread Result --}}
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                Thread
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                in {{ $result['item']->forum->name }}
                                            </span>
                                        </div>
                                        <a href="{{ route('forums.threads.show', [$result['item']->forum->forumGroup->slug, $result['item']->forum->slug, $result['item']->slug]) }}" 
                                           class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 block"
                                           wire:click="clearSearch">
                                            {{ $result['item']->title }}
                                        </a>
                                        @if($result['excerpt'])
                                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $result['excerpt'] }}</p>
                                        @endif
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            by {{ $result['item']->user->name }} • {{ $result['item']->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Post Result --}}
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Post
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                in {{ $result['item']->thread->forum->name }}
                                            </span>
                                        </div>
                                        <a href="{{ route('forums.threads.show', [$result['item']->thread->forum->forumGroup->slug, $result['item']->thread->forum->slug, $result['item']->thread->slug]) }}" 
                                           class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 block"
                                           wire:click="clearSearch">
                                            Re: {{ $result['item']->thread->title }}
                                        </a>
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $result['excerpt'] }}</p>
                                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            by {{ $result['item']->user->name }} • {{ $result['item']->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No results found</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Try different search terms or change the search type.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Overlay to close search on outside click --}}
    @if($showResults && $query)
        <div 
            wire:click="clearSearch"
            class="fixed inset-0 z-40"
        ></div>
    @endif
</div>