@props(['groups', 'selectedGroup' => '', 'sortBy' => 'latest', 'search' => '', 'showSearch' => true])

<div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
    {{-- Search --}}
    @if($showSearch)
        <div class="mb-4">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                Search Plugins
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       id="search"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Search by name or description..."
                       value="{{ $search }}">
            </div>
        </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Group Filter --}}
        <div>
            <label for="group-filter" class="block text-sm font-medium text-gray-700 mb-2">
                Category
            </label>
            <select wire:model.live="selectedGroup" 
                    id="group-filter"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
        
        {{-- Actions --}}
        <div class="flex items-end">
            <button wire:click="clearFilters" 
                    type="button"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <x-heroicon-o-x-mark class="h-4 w-4 mr-2" />
                Clear Filters
            </button>
        </div>
    </div>
    
    {{-- Active Filters Display --}}
    @if($selectedGroup || $search || $sortBy !== 'latest')
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-500">Active filters:</span>
                    
                    @if($search)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Search: "{{ $search }}"
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