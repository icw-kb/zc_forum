@props(['plugin'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow duration-200">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-puzzle-piece class="w-6 h-6 text-white" />
                    </div>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                            <a href="{{ route('plugins.show', $plugin->slug) }}" 
                               class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150">
                                {{ $plugin->name }}
                            </a>
                        </h3>
                        
                        @if($plugin->is_featured)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <x-heroicon-o-star class="w-3 h-3 mr-1" />
                                Featured
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-4 mt-1">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $plugin->group->name ?? 'Uncategorized' }}
                        </span>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center">
                                <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                                {{ number_format($plugin->view_count) }}
                            </span>
                            <span class="flex items-center">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                                {{ number_format($plugin->download_count) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <p class="mt-3 text-gray-600 dark:text-gray-300 text-sm line-clamp-2">
                {{ $plugin->description }}
            </p>
        </div>
        
        <div class="flex-shrink-0 ml-6 flex items-center space-x-2">
            <a href="{{ route('plugins.show', $plugin->slug) }}" 
               class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <x-heroicon-o-eye class="w-4 h-4 mr-2" />
                View
            </a>
            
            @auth
                @php
                    $latestVersion = $plugin->latestVersion;
                @endphp
                
                @if($latestVersion)
                    <a href="{{ route('plugins.download.direct', ['plugin' => $plugin->slug, 'version' => $latestVersion->version]) }}" 
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                        Download
                    </a>
                @endif
            @else
                <button onclick="$dispatch('open-modal', 'login')" 
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gray-400 hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-150">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                    Download
                </button>
            @endauth
        </div>
    </div>
</div>