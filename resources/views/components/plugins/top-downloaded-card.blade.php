<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2 text-gray-400 dark:text-gray-500" />
            Top Downloads
        </h3>
        
        @if($plugins->count() > 0)
            <div class="mt-4 space-y-3">
                @foreach($plugins as $index => $plugin)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <span class="flex-shrink-0 text-sm font-medium text-gray-500 dark:text-gray-400 w-6">
                                {{ $index + 1 }}.
                            </span>
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
        @else
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                No downloads yet.
            </p>
        @endif
    </div>
</div>