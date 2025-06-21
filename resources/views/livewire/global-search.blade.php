<div class="relative w-full max-w-2xl mx-auto" x-data="{ showDropdown: $wire.entangle('showDropdown'), showAdvanced: $wire.entangle('showAdvanced') }">
    <form wire:submit.prevent="submitSearch" class="relative">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500" />
            </div>
            
            <input 
                type="text" 
                wire:model.live.debounce.300ms="query"
                wire:loading.attr="disabled"
                wire:target="updatedQuery"
                placeholder="Search plugins, forums, threads, and more..."
                class="block w-full pl-12 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all duration-150 shadow-sm hover:shadow-md focus:shadow-lg disabled:bg-gray-50 dark:disabled:bg-gray-700 disabled:cursor-wait"
                autocomplete="off"
                @click.away="$wire.hideDropdown()"
                @keydown.escape="$wire.hideDropdown()"
            >
            
            <div class="absolute inset-y-0 right-0 flex items-center">
                <div wire:loading wire:target="updatedQuery" class="pr-4">
                    <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                </div>
                
                @if($query)
                    <button 
                        type="button" 
                        wire:click="$set('query', '')"
                        class="pr-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-150"
                    >
                        <x-heroicon-o-x-mark class="h-4 w-4" />
                    </button>
                @endif
                
                <button 
                    type="submit"
                    class="mr-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Search
                </button>
            </div>
        </div>
        
        {{-- Advanced Search Toggle --}}
        <div class="mt-2 text-right">
            <button 
                type="button"
                wire:click="toggleAdvanced"
                class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors duration-150"
            >
                <span x-show="!showAdvanced">
                    <x-heroicon-o-adjustments-horizontal class="h-4 w-4 inline-block mr-1" />
                    Advanced
                </span>
                <span x-show="showAdvanced">
                    <x-heroicon-o-adjustments-horizontal class="h-4 w-4 inline-block mr-1" />
                    Simple
                </span>
            </button>
        </div>
        
        {{-- Advanced Search Options --}}
        <div 
            x-show="showAdvanced"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700"
            style="display: none;"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Search In --}}
                <div>
                    <label for="searchIn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Search in
                    </label>
                    <select 
                        id="searchIn"
                        wire:model.live="searchIn"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                    >
                        <option value="all">All content</option>
                        <option value="plugins">Plugins only</option>
                        <option value="forums">Forums only</option>
                    </select>
                </div>
                
                {{-- Plugin Group (shown when searching plugins) --}}
                @if($currentContext === 'plugins' || $searchIn === 'plugins')
                    <div>
                        <label for="pluginGroup" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Plugin category
                        </label>
                        <select 
                            id="pluginGroup"
                            wire:model.live="pluginGroup"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="">All categories</option>
                            @foreach($pluginGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                {{-- Forum Group (shown when searching forums) --}}
                @if($currentContext === 'forums' || $searchIn === 'forums')
                    <div>
                        <label for="forumGroup" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Forum category
                        </label>
                        <select 
                            id="forumGroup"
                            wire:model.live="forumGroup"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="">All categories</option>
                            @foreach($forumGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                {{-- Date Range (shown for forums) --}}
                @if($currentContext === 'forums' || $searchIn === 'forums' || $searchIn === 'all')
                    <div>
                        <label for="dateRange" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Date range
                        </label>
                        <select 
                            id="dateRange"
                            wire:model.live="dateRange"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        >
                            <option value="all">Any time</option>
                            <option value="today">Today</option>
                            <option value="week">Past week</option>
                            <option value="month">Past month</option>
                            <option value="year">Past year</option>
                        </select>
                    </div>
                @endif
                
                {{-- Author (shown for forums) --}}
                @if($currentContext === 'forums' || $searchIn === 'forums' || $searchIn === 'all')
                    <div>
                        <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Author
                        </label>
                        <input 
                            type="text"
                            id="author"
                            wire:model.live.debounce.300ms="author"
                            placeholder="Username"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
                        >
                    </div>
                @endif
            </div>
            
            {{-- Clear Filters --}}
            @if($searchIn !== 'all' || $pluginGroup || $forumGroup || $dateRange !== 'all' || $author)
                <div class="mt-3 text-right">
                    <button 
                        type="button"
                        wire:click="$set('searchIn', 'all'); $set('pluginGroup', ''); $set('forumGroup', ''); $set('dateRange', 'all'); $set('author', '');"
                        class="text-sm text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150"
                    >
                        Clear filters
                    </button>
                </div>
            @endif
        </div>
    </form>

    {{-- Search Results Dropdown --}}
    @if(count(array_filter($results)) > 0)
    <div 
        x-show="showDropdown"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute z-40 w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto"
        style="display: none;"
    >
        <div class="py-2">
            {{-- Plugins --}}
            @if(!empty($results['plugins']))
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Plugins</h4>
                </div>
                @foreach($results['plugins'] as $result)
                    <a href="{{ $result['url'] }}" 
                       class="flex items-start px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                       wire:click="hideDropdown">
                        <div class="flex-shrink-0 mt-0.5">
                            <x-dynamic-component :component="$result['icon']" class="h-4 w-4 text-blue-500" />
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['title'] }}
                            </p>
                            @if($result['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $result['description'] }}
                                </p>
                            @endif
                            @if($result['meta'])
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $result['meta'] }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif

            {{-- Plugin Versions --}}
            @if(!empty($results['plugin_versions']))
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Plugin Versions</h4>
                </div>
                @foreach($results['plugin_versions'] as $result)
                    <a href="{{ $result['url'] }}" 
                       class="flex items-start px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                       wire:click="hideDropdown">
                        <div class="flex-shrink-0 mt-0.5">
                            <x-dynamic-component :component="$result['icon']" class="h-4 w-4 text-green-500" />
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['title'] }}
                            </p>
                            @if($result['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $result['description'] }}
                                </p>
                            @endif
                            @if($result['meta'])
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $result['meta'] }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif

            {{-- Threads --}}
            @if(!empty($results['threads']))
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Threads</h4>
                </div>
                @foreach($results['threads'] as $result)
                    <a href="{{ $result['url'] }}" 
                       class="flex items-start px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                       wire:click="hideDropdown">
                        <div class="flex-shrink-0 mt-0.5">
                            <x-dynamic-component :component="$result['icon']" class="h-4 w-4 text-purple-500" />
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['title'] }}
                            </p>
                            @if($result['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    in {{ $result['description'] }}
                                </p>
                            @endif
                            @if($result['meta'])
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $result['meta'] }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif

            {{-- Posts --}}
            @if(!empty($results['posts']))
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Posts</h4>
                </div>
                @foreach($results['posts'] as $result)
                    <a href="{{ $result['url'] }}" 
                       class="flex items-start px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                       wire:click="hideDropdown">
                        <div class="flex-shrink-0 mt-0.5">
                            <x-dynamic-component :component="$result['icon']" class="h-4 w-4 text-orange-500" />
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['title'] }}
                            </p>
                            @if($result['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ $result['description'] }}
                                </p>
                            @endif
                            @if($result['meta'])
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $result['meta'] }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif

            {{-- Forums --}}
            @if(!empty($results['forums']))
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Forums</h4>
                </div>
                @foreach($results['forums'] as $result)
                    <a href="{{ $result['url'] }}" 
                       class="flex items-start px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                       wire:click="hideDropdown">
                        <div class="flex-shrink-0 mt-0.5">
                            <x-dynamic-component :component="$result['icon']" class="h-4 w-4 text-indigo-500" />
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['title'] }}
                            </p>
                            @if($result['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $result['description'] }}
                                </p>
                            @endif
                            @if($result['meta'])
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    in {{ $result['meta'] }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif

            {{-- Forum Groups & Plugin Groups --}}
            @if(!empty($results['forum_groups']) || !empty($results['plugin_groups']))
                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Categories</h4>
                </div>
                @foreach(array_merge($results['forum_groups'] ?? [], $results['plugin_groups'] ?? []) as $result)
                    <a href="{{ $result['url'] }}" 
                       class="flex items-start px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                       wire:click="hideDropdown">
                        <div class="flex-shrink-0 mt-0.5">
                            <x-dynamic-component :component="$result['icon']" class="h-4 w-4 text-gray-500" />
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $result['title'] }}
                            </p>
                            @if($result['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $result['description'] }}
                                </p>
                            @endif
                            @if($result['meta'])
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $result['meta'] }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif

            {{-- View All Results --}}
            @if($query && count(array_filter($results)) > 0)
                <div class="border-t border-gray-100 dark:border-gray-700">
                    <button 
                        wire:click="submitSearch"
                        class="w-full px-4 py-3 text-left text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                    >
                        View all results for "{{ $query }}" →
                    </button>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- No Results Message --}}
    @if(count(array_filter($results)) === 0 && $query !== '')
    <div 
        x-show="showDropdown"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute z-40 w-full mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700"
        style="display: none;"
    >
        <div class="px-4 py-6 text-center">
            <x-heroicon-o-magnifying-glass class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500 mb-2" />
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No results found for "{{ $query }}"
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                Try different keywords or check your spelling
            </p>
        </div>
    </div>
    @endif
</div>