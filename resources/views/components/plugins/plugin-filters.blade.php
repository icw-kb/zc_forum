@props(['groups', 'selectedGroup' => '', 'sortBy' => 'latest', 'search' => '', 'showSearch' => true])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6 transition-all duration-200">
    {{-- Search --}}
    @if($showSearch)
        <div class="mb-4 sm:mb-6">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Search Plugins
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       wire:loading.attr="disabled"
                       wire:target="search"
                       id="search"
                       class="block w-full pl-10 pr-3 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-150 disabled:bg-gray-50 dark:disabled:bg-gray-600 disabled:cursor-wait"
                       placeholder="Search by name or description..."
                       value="{{ $search }}">
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <div class="animate-spin h-4 w-4 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                </div>
            </div>
        </div>
    @endif
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Group Filter --}}
        <div>
            <label for="group-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Category
            </label>
            <select wire:model.live="selectedGroup" 
                    wire:loading.attr="disabled"
                    wire:target="selectedGroup"
                    id="group-filter"
                    class="block w-full px-3 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-150 disabled:bg-gray-50 dark:disabled:bg-gray-600 disabled:cursor-wait">
                <option value="">All Categories</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" @selected($selectedGroup == $group->id)>
                        {{ $group->name }} ({{ $group->plugin_count }})
                    </option>
                @endforeach
            </select>
        </div>
        
        {{-- Sort Filter --}}
        <div>
            <label for="sort-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Sort By
            </label>
            <select wire:model.live="sortBy" 
                    wire:loading.attr="disabled"
                    wire:target="sortBy"
                    id="sort-filter"
                    class="block w-full px-3 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all duration-150 disabled:bg-gray-50 dark:disabled:bg-gray-600 disabled:cursor-wait">
                <option value="latest" @selected($sortBy == 'latest')>Latest</option>
                <option value="downloads" @selected($sortBy == 'downloads')>Most Downloaded</option>
                <option value="views" @selected($sortBy == 'views')>Most Viewed</option>
                <option value="name" @selected($sortBy == 'name')>Name A-Z</option>
                <option value="is_featured" @selected($sortBy == 'is_featured')>Featured First</option>
            </select>
        </div>
        
        {{-- Actions --}}
        <div class="flex items-end">
            <button wire:click="clearFilters" 
                    wire:loading.attr="disabled"
                    type="button"
                    class="inline-flex items-center px-4 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                <x-heroicon-o-x-mark class="h-4 w-4 mr-2" />
                Clear Filters
            </button>
        </div>
    </div>
    
    {{-- Active Filters Display --}}
    @if($selectedGroup || $sortBy !== 'latest')
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Active filters:</span>
                    
                    @if($selectedGroup)
                        @php
                            $selectedGroupName = $groups->firstWhere('id', $selectedGroup)->name ?? 'Unknown';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Category: {{ $selectedGroupName }}
                        </span>
                    @endif
                    
                    @if($sortBy !== 'latest')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            Sort: {{ ucwords(str_replace('_', ' ', $sortBy)) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>