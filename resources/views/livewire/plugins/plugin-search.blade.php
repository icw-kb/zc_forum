<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Search Plugins</h1>
        <p class="mt-2 text-lg text-gray-600">
            Find the perfect plugin for your Zen Cart store.
        </p>
    </div>
    
    {{-- Search Form --}}
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Search Input --}}
            <div class="md:col-span-2">
                <label for="search-query" class="block text-sm font-medium text-gray-700 mb-2">
                    Search Query
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="query"
                           id="search-query"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-lg"
                           placeholder="Search for plugins..."
                           value="{{ $query }}">
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Search by plugin name, description, or keywords
                </p>
            </div>
            
            {{-- Category Filter --}}
            <div>
                <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-2">
                    Category
                </label>
                <select wire:model.live="selectedGroup" 
                        id="category-filter"
                        class="block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" @selected($selectedGroup == $group->id)>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        {{-- Advanced Options --}}
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label for="sort-results" class="block text-sm font-medium text-gray-700">
                        Sort by:
                    </label>
                    <select wire:model.live="sortBy" 
                            id="sort-results"
                            class="px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="relevance" @selected($sortBy == 'relevance')>Relevance</option>
                        <option value="downloads" @selected($sortBy == 'downloads')>Most Downloaded</option>
                        <option value="views" @selected($sortBy == 'views')>Most Viewed</option>
                        <option value="latest" @selected($sortBy == 'latest')>Latest</option>
                        <option value="name" @selected($sortBy == 'name')>Name A-Z</option>
                    </select>
                </div>
                
                @if($query || $selectedGroup)
                    <button wire:click="clearFilters" 
                            type="button"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <x-heroicon-o-x-mark class="h-4 w-4 mr-1" />
                        Clear all
                    </button>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Search Results --}}
    @if($hasQuery)
        {{-- Results Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    Search Results for "{{ $query }}"
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Found {{ number_format($resultCount) }} {{ Str::plural('plugin', $resultCount) }}
                    @if($selectedGroup)
                        @php
                            $selectedGroupName = $groups->firstWhere('id', $selectedGroup)->name ?? 'Unknown';
                        @endphp
                        in {{ $selectedGroupName }}
                    @endif
                </p>
            </div>
        </div>
        
        {{-- Active Filters --}}
        @if($query || $selectedGroup)
            <div class="mb-6">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-500">Active filters:</span>
                    
                    @if($query)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Query: "{{ $query }}"
                        </span>
                    @endif
                    
                    @if($selectedGroup)
                        @php
                            $selectedGroupName = $groups->firstWhere('id', $selectedGroup)->name ?? 'Unknown';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Category: {{ $selectedGroupName }}
                        </span>
                    @endif
                    
                    @if($sortBy !== 'relevance')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Sort: {{ ucwords(str_replace('_', ' ', $sortBy)) }}
                        </span>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Results Grid --}}
        @if($results->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($results as $plugin)
                    <x-plugins.plugin-card :plugin="$plugin" />
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-lg">
                <div class="flex flex-1 justify-between sm:hidden">
                    @if($results->previousPageUrl())
                        <a href="{{ $results->previousPageUrl() }}" 
                           class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($results->nextPageUrl())
                        <a href="{{ $results->nextPageUrl() }}" 
                           class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $results->firstItem() }}</span> to 
                            <span class="font-medium">{{ $results->lastItem() }}</span> of 
                            <span class="font-medium">{{ $results->total() }}</span> results
                        </p>
                    </div>
                    
                    <div>
                        {{ $results->links() }}
                    </div>
                </div>
            </div>
        @else
            {{-- No Results --}}
            <div class="text-center py-12">
                <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No plugins found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    No plugins match your search criteria. Try different keywords or browse all plugins.
                </p>
                
                <div class="mt-6 flex justify-center space-x-3">
                    <button wire:click="clearFilters" 
                            type="button"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Clear search
                    </button>
                    
                    <a href="{{ route('plugins.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Browse all plugins
                    </a>
                </div>
            </div>
        @endif
        
    @else
        {{-- Search Suggestions --}}
        <div class="bg-gray-50 rounded-lg p-8">
            <div class="text-center">
                <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-lg font-medium text-gray-900">Start searching for plugins</h3>
                <p class="mt-1 text-gray-500">
                    Enter at least {{ $minQueryLength }} characters to search for plugins.
                </p>
            </div>
            
            {{-- Popular Categories --}}
            @if($groups->count() > 0)
                <div class="mt-8">
                    <h4 class="text-sm font-medium text-gray-900 mb-4 text-center">Popular Categories</h4>
                    <div class="flex flex-wrap justify-center gap-2">
                        @foreach($groups->take(6) as $group)
                            <button wire:click="$set('selectedGroup', {{ $group->id }})" 
                                    type="button"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $group->name }}
                                <span class="ml-1 text-xs text-gray-500">({{ $group->plugin_count }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Search Tips --}}
            <div class="mt-8 text-center">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Search Tips</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div>
                        <div class="font-medium mb-1">Use specific terms</div>
                        <div>"payment gateway" instead of "payment"</div>
                    </div>
                    <div>
                        <div class="font-medium mb-1">Try different words</div>
                        <div>"shipping" or "delivery" or "logistics"</div>
                    </div>
                    <div>
                        <div class="font-medium mb-1">Browse categories</div>
                        <div>Filter by category for better results</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
