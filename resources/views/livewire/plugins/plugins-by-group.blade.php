<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('plugins.index') }}" class="text-gray-500 hover:text-gray-700">
                    <x-heroicon-o-home class="flex-shrink-0 h-5 w-5" />
                    <span class="sr-only">Plugins</span>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <x-heroicon-o-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
                    <a href="{{ route('plugins.index') }}" class="ml-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Plugins
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <x-heroicon-o-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400" />
                    <span class="ml-2 text-sm font-medium text-gray-900">{{ $group->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            {{-- Current Group Info --}}
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">{{ $group->name }}</h2>
                </div>
                <div class="px-6 py-4">
                    @if($group->description)
                        <p class="text-sm text-gray-600 mb-4">{{ $group->description }}</p>
                    @endif
                    
                    <div class="flex items-center text-sm text-gray-500">
                        <x-heroicon-o-puzzle-piece class="w-4 h-4 mr-2" />
                        {{ $plugins->total() }} {{ Str::plural('plugin', $plugins->total()) }}
                    </div>
                </div>
            </div>
            
            {{-- All Categories --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">All Categories</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <div class="px-6 py-3">
                        <a href="{{ route('plugins.index') }}" 
                           class="flex items-center justify-between text-sm hover:text-blue-600">
                            <span>All Plugins</span>
                            <span class="text-gray-400">{{ $allGroups->sum('plugin_count') }}</span>
                        </a>
                    </div>
                    
                    @foreach($allGroups as $categoryGroup)
                        <div class="px-6 py-3">
                            <a href="{{ route('plugins.group', $categoryGroup->slug) }}" 
                               class="flex items-center justify-between text-sm hover:text-blue-600 
                                      {{ $categoryGroup->id === $group->id ? 'text-blue-600 font-medium' : 'text-gray-700' }}">
                                <span>{{ $categoryGroup->name }}</span>
                                <span class="text-gray-400">{{ $categoryGroup->plugin_count }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        {{-- Main Content --}}
        <div class="lg:col-span-3">
            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $group->name }} Plugins</h1>
                <p class="mt-2 text-lg text-gray-600">
                    Browse plugins in the {{ $group->name }} category.
                </p>
            </div>
            
            {{-- Search and Sort --}}
            <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Search --}}
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            Search in {{ $group->name }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                            </div>
                            <input type="text" 
                                   wire:model.live.debounce.300ms="search"
                                   id="search"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="Search plugins..."
                                   value="{{ $search }}">
                        </div>
                    </div>
                    
                    {{-- Sort --}}
                    <div>
                        <label for="sort-filter" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort By
                        </label>
                        <select wire:model.live="sortBy" 
                                id="sort-filter"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="latest" @selected($sortBy == 'latest')>Latest</option>
                            <option value="downloads" @selected($sortBy == 'downloads')>Most Downloaded</option>
                            <option value="views" @selected($sortBy == 'views')>Most Viewed</option>
                            <option value="name" @selected($sortBy == 'name')>Name A-Z</option>
                            <option value="featured" @selected($sortBy == 'featured')>Featured First</option>
                        </select>
                    </div>
                </div>
                
                {{-- Active Filters --}}
                @if($search || $sortBy !== 'latest')
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-wrap gap-2">
                                <span class="text-sm text-gray-500">Active filters:</span>
                                
                                @if($search)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Search: "{{ $search }}"
                                    </span>
                                @endif
                                
                                @if($sortBy !== 'latest')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Sort: {{ ucwords(str_replace('_', ' ', $sortBy)) }}
                                    </span>
                                @endif
                            </div>
                            
                            <button wire:click="clearSearch" 
                                    type="button"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <x-heroicon-o-x-mark class="h-3 w-3 mr-1" />
                                Clear
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Results Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-medium text-gray-900">
                        @if($search)
                            Search Results for "{{ $search }}"
                        @else
                            All Plugins
                        @endif
                    </h2>
                    
                    <span class="text-sm text-gray-500">
                        {{ $plugins->total() }} {{ Str::plural('plugin', $plugins->total()) }}
                    </span>
                </div>
            </div>
            
            {{-- Plugin Grid --}}
            @if($plugins->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($plugins as $plugin)
                        <x-plugins.plugin-card :plugin="$plugin" :showGroup="false" />
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 rounded-lg">
                    <div class="flex flex-1 justify-between sm:hidden">
                        @if($plugins->previousPageUrl())
                            <a href="{{ $plugins->previousPageUrl() }}" 
                               class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        @endif
                        
                        @if($plugins->nextPageUrl())
                            <a href="{{ $plugins->nextPageUrl() }}" 
                               class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        @endif
                    </div>
                    
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $plugins->firstItem() }}</span> to 
                                <span class="font-medium">{{ $plugins->lastItem() }}</span> of 
                                <span class="font-medium">{{ $plugins->total() }}</span> results
                            </p>
                        </div>
                        
                        <div>
                            {{ $plugins->links() }}
                        </div>
                    </div>
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No plugins found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search)
                            No plugins match your search criteria in {{ $group->name }}.
                        @else
                            There are no plugins in the {{ $group->name }} category yet.
                        @endif
                    </p>
                    
                    @if($search)
                        <div class="mt-6">
                            <button wire:click="clearSearch" 
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear search
                            </button>
                        </div>
                    @else
                        <div class="mt-6">
                            <a href="{{ route('plugins.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Browse all plugins
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
