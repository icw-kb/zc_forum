<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Zen Cart Plugins</h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
            Discover and download plugins to extend your Zen Cart store functionality.
        </p>
        
        {{-- Quick Stats --}}
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-puzzle-piece class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Plugins</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $plugins->total() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-folder class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Categories</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $groups->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-heroicon-o-star class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Featured</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $featuredPluginsCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Featured Plugins Section --}}
    @if($featuredPlugins->count() > 0)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Featured Plugins</h2>
                <a href="{{ route('plugins.index', ['sortBy' => 'featured']) }}" 
                   class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                    View all featured →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredPlugins as $plugin)
                    <x-plugins.plugin-card :plugin="$plugin" />
                @endforeach
            </div>
        </div>
    @endif
    
    {{-- Filters --}}
    <x-plugins.plugin-filters 
        :groups="$groups" 
        :selectedGroup="$selectedGroup" 
        :sortBy="$sortBy" 
        :search="$search" 
    />
    
    {{-- Results Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                @if($search)
                    Search Results for "{{ $search }}"
                @elseif($selectedGroup)
                    @php
                        $selectedGroupName = $groups->firstWhere('id', $selectedGroup)->name ?? 'Selected Category';
                    @endphp
                    {{ $selectedGroupName }} Plugins
                @else
                    All Plugins
                @endif
            </h2>
            
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $plugins->total() }} {{ Str::plural('plugin', $plugins->total()) }}
            </span>
        </div>
        
        {{-- View Toggle --}}
        <div class="hidden sm:flex items-center space-x-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">View:</span>
            <div class="flex rounded-md shadow-sm">
                <button type="button" 
                        wire:click="setViewMode('grid')"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 text-sm font-medium transition-colors duration-150 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
                               {{ $viewMode === 'grid' ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400 border-blue-500' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                    <x-heroicon-o-squares-2x2 class="h-4 w-4" />
                    <span class="sr-only">Grid view</span>
                </button>
                <button type="button" 
                        wire:click="setViewMode('list')"
                        class="relative inline-flex items-center px-2 py-2 -ml-px rounded-r-md border border-gray-300 dark:border-gray-600 text-sm font-medium transition-colors duration-150 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               {{ $viewMode === 'list' ? 'bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400 border-blue-500' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                    <x-heroicon-o-list-bullet class="h-4 w-4" />
                    <span class="sr-only">List view</span>
                </button>
            </div>
        </div>
    </div>
    
    {{-- Loading State --}}
    <div wire:loading wire:target="search,selectedGroup,sortBy" class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-blue-500 dark:text-blue-400 bg-white dark:bg-gray-700">
                <div class="animate-spin -ml-1 mr-3 h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                Loading plugins...
            </div>
        </div>
    </div>
    
    {{-- Plugin Grid/List --}}
    <div wire:loading.remove wire:target="search,selectedGroup,sortBy">
        @if($plugins->count() > 0)
            @if($viewMode === 'list')
                <div class="space-y-4 mb-8">
                    @foreach($plugins as $plugin)
                        <x-plugins.plugin-list-item :plugin="$plugin" />
                    @endforeach
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($plugins as $plugin)
                        <x-plugins.plugin-card :plugin="$plugin" />
                    @endforeach
                </div>
            @endif
        
            {{-- Pagination --}}
            <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6 rounded-lg shadow-sm">
                <div class="flex flex-1 justify-between sm:hidden">
                    @if($plugins->previousPageUrl())
                        <a href="{{ $plugins->previousPageUrl() }}" 
                           class="relative inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                            <x-heroicon-o-chevron-left class="w-4 h-4 mr-1" />
                            Previous
                        </a>
                    @endif
                    
                    @if($plugins->nextPageUrl())
                        <a href="{{ $plugins->nextPageUrl() }}" 
                           class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                            Next
                            <x-heroicon-o-chevron-right class="w-4 h-4 ml-1" />
                        </a>
                    @endif
                </div>
                
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
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
            <div class="text-center py-12 px-4">
                <div class="max-w-sm mx-auto">
                    <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">No plugins found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        @if($search || $selectedGroup)
                            Try adjusting your search criteria or browse all plugins.
                        @else
                            Get started by adding some plugins to the system.
                        @endif
                    </p>
                    
                    @if($search || $selectedGroup)
                        <div class="mt-6">
                            <button wire:click="clearFilters" 
                                    type="button"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                                <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                                Clear filters
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
