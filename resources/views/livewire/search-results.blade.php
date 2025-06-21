<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
            @if($query)
                Search Results for "{{ $query }}"
            @else
                Search
            @endif
        </h1>
        @if($query && $results->count() > 0)
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
                Found {{ $results->count() }} {{ Str::plural('result', $results->count()) }}
            </p>
        @endif
    </div>

    @if($query)
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            {{-- Filters Sidebar --}}
            <div class="lg:col-span-3 mb-8 lg:mb-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Filter Results</h3>
                    
                    {{-- Content Type Filter --}}
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content Type</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="activeFilter" value="all" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">All Results</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="activeFilter" value="plugins" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Plugins</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="activeFilter" value="plugin_versions" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Plugin Versions</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="activeFilter" value="threads" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Threads</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="activeFilter" value="posts" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Posts</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="activeFilter" value="forums" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Forums</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model.live="activeFilter" value="categories" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Categories</span>
                            </label>
                        </div>
                    </div>

                    {{-- Sort Options --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort By</label>
                        <select wire:model.live="sortBy" 
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="relevance">Relevance</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="updated">Recently Updated</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Results --}}
            <div class="lg:col-span-9">
                @if($results->count() > 0)
                    <div class="space-y-6">
                        @foreach($results as $result)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
                                {{-- Result Header --}}
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        {{-- Type Icon --}}
                                        @switch($result['type'])
                                            @case('plugin')
                                                <x-heroicon-o-puzzle-piece class="h-5 w-5 text-blue-500" />
                                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Plugin</span>
                                                @break
                                            @case('plugin_version')
                                                <x-heroicon-o-tag class="h-5 w-5 text-green-500" />
                                                <span class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Plugin Version</span>
                                                @break
                                            @case('thread')
                                                <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 text-purple-500" />
                                                <span class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Thread</span>
                                                @break
                                            @case('post')
                                                <x-heroicon-o-document-text class="h-5 w-5 text-orange-500" />
                                                <span class="text-xs font-medium text-orange-600 dark:text-orange-400 uppercase tracking-wide">Post</span>
                                                @break
                                            @case('forum')
                                                <x-heroicon-o-rectangle-stack class="h-5 w-5 text-indigo-500" />
                                                <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">Forum</span>
                                                @break
                                            @case('forum_group')
                                                <x-heroicon-o-folder class="h-5 w-5 text-gray-500" />
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Forum Category</span>
                                                @break
                                            @case('plugin_group')
                                                <x-heroicon-o-folder-open class="h-5 w-5 text-gray-500" />
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Plugin Category</span>
                                                @break
                                        @endswitch
                                    </div>
                                    
                                    {{-- Featured Badge --}}
                                    @if(($result['meta']['featured'] ?? false) && $result['type'] === 'plugin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <x-heroicon-s-star class="w-3 h-3 mr-1" />
                                            Featured
                                        </span>
                                    @endif
                                </div>

                                {{-- Title and Description --}}
                                <div class="mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        <a href="{{ $result['url'] }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150">
                                            {{ $result['title'] }}
                                        </a>
                                    </h3>
                                    @if($result['description'])
                                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                            {{ $result['description'] }}
                                        </p>
                                    @endif
                                </div>

                                {{-- Metadata --}}
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if(isset($result['meta']['group']))
                                        <span class="flex items-center">
                                            <x-heroicon-o-folder class="w-4 h-4 mr-1" />
                                            {{ $result['meta']['group'] }}
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['meta']['forum']))
                                        <span class="flex items-center">
                                            <x-heroicon-o-rectangle-stack class="w-4 h-4 mr-1" />
                                            {{ $result['meta']['forum'] }}
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['meta']['user']))
                                        <span class="flex items-center">
                                            <x-heroicon-o-user class="w-4 h-4 mr-1" />
                                            {{ $result['meta']['user'] }}
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['meta']['downloads']))
                                        <span class="flex items-center">
                                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                                            {{ $result['meta']['downloads'] }} downloads
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['meta']['views']))
                                        <span class="flex items-center">
                                            <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                            {{ $result['meta']['views'] }} views
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['meta']['posts']))
                                        <span class="flex items-center">
                                            <x-heroicon-o-chat-bubble-left-right class="w-4 h-4 mr-1" />
                                            {{ $result['meta']['posts'] }} posts
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['meta']['likes']) && $result['meta']['likes'] > 0)
                                        <span class="flex items-center">
                                            <x-heroicon-o-heart class="w-4 h-4 mr-1" />
                                            {{ $result['meta']['likes'] }} likes
                                        </span>
                                    @endif
                                    
                                    @if(($result['meta']['accepted'] ?? false) && $result['type'] === 'post')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <x-heroicon-s-check-circle class="w-3 h-3 mr-1" />
                                            Accepted Answer
                                        </span>
                                    @endif
                                    
                                    @if(($result['meta']['pinned'] ?? false) && $result['type'] === 'thread')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <x-heroicon-s-bookmark class="w-3 h-3 mr-1" />
                                            Pinned
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['created_at']))
                                        <span class="flex items-center">
                                            <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                                            {{ $result['created_at']->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if(method_exists($results, 'links'))
                        <div class="mt-8">
                            {{ $results->links() }}
                        </div>
                    @endif
                @else
                    {{-- No Results --}}
                    <div class="text-center py-12">
                        <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No results found</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                            We couldn't find anything matching "{{ $query }}". Try different keywords or check your spelling.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('plugins.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Browse Plugins
                            </a>
                            <a href="{{ route('forums.index') }}" 
                               class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Browse Forums
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <x-heroicon-o-magnifying-glass class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500 mb-4" />
            <h3 class="text-2xl font-medium text-gray-900 dark:text-gray-100 mb-2">Search our community</h3>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-8">
                Find plugins, forum discussions, answers to questions, and more across our entire platform.
            </p>
            {{-- Search tips --}}
            <div class="max-w-3xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">Search Tips</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                            <li>• Use specific keywords</li>
                            <li>• Try different spellings</li>
                            <li>• Use quotes for exact phrases</li>
                            <li>• Search for plugin names or features</li>
                        </ul>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-2">What You Can Find</h4>
                        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                            <li>• Zen Cart plugins and extensions</li>
                            <li>• Forum discussions and solutions</li>
                            <li>• User posts and answers</li>
                            <li>• Categories and groups</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>