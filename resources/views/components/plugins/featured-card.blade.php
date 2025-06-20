<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
            <x-heroicon-o-star class="w-5 h-5 mr-2 text-yellow-400" />
            Featured Plugins
        </h3>
        
        @if($plugins->count() > 0)
            <div class="mt-4 space-y-3">
                @foreach($plugins as $plugin)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0">
                                <x-heroicon-s-star class="w-4 h-4 text-yellow-400" />
                            </div>
                            <a href="{{ route('plugins.show', $plugin->slug) }}" 
                               class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 truncate">
                                {{ $plugin->name }}
                            </a>
                        </div>
                        <span class="ml-2 flex-shrink-0 text-sm text-gray-500 dark:text-gray-400">
                            {{ number_format($plugin->download_count) }}
                        </span>
                    </div>
                @endforeach
            </div>
            
            @if($plugins->count() >= 5)
                <div class="mt-4">
                    <a href="{{ route('plugins.index', ['sortBy' => 'is_featured']) }}" 
                       class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium">
                        View all featured →
                    </a>
                </div>
            @endif
        @else
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                No featured plugins yet.
            </p>
        @endif
    </div>
</div>