@props(['plugin', 'showGroup' => true])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 flex flex-col h-full">
    {{-- Plugin Header --}}
    <div class="p-6 flex-1">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-1 line-clamp-2">
                    <a href="{{ route('plugins.show', $plugin->slug) }}" 
                       class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                        {{ $plugin->name }}
                    </a>
                </h3>
                
                @if($showGroup && $plugin->group)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $plugin->group->name }}
                    </span>
                @endif
            </div>
            
            @if($plugin->featured)
                <div class="ml-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <x-heroicon-s-star class="w-3 h-3 mr-1" />
                        Featured
                    </span>
                </div>
            @endif
        </div>
        
        {{-- Description --}}
        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3 leading-relaxed">
            {{ $plugin->description ?: 'No description available.' }}
        </p>
        
        {{-- Plugin Statistics --}}
        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
            <div class="flex items-center space-x-4">
                <span class="flex items-center">
                    <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                    {{ number_format($plugin->view_count) }}
                </span>
                <span class="flex items-center">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                    {{ number_format($plugin->download_count) }}
                </span>
            </div>
            
            @if($plugin->latestVersion)
                <span class="text-xs bg-gray-100 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 rounded">
                    v{{ $plugin->latestVersion->version }}
                </span>
            @endif
        </div>
        
        {{-- GitHub Link --}}
        @if($plugin->github_url)
            <div class="mb-4">
                <a href="{{ $plugin->github_url }}" 
                   target="_blank" 
                   class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                    <x-heroicon-o-code-bracket class="w-4 h-4 mr-1" />
                    View on GitHub
                </a>
            </div>
        @endif
        
        {{-- Compatible Zen Cart Versions --}}
        @if($plugin->latestVersion && $plugin->latestVersion->zencartVersions->count() > 0)
            <div class="mb-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Compatible with:</div>
                <div class="flex flex-wrap gap-1">
                    @foreach($plugin->latestVersion->zencartVersions->take(3) as $version)
                        <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                            ZC {{ $version->version }}
                        </span>
                    @endforeach
                    @if($plugin->latestVersion->zencartVersions->count() > 3)
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            +{{ $plugin->latestVersion->zencartVersions->count() - 3 }} more
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    {{-- Card Footer --}}
    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 border-t dark:border-gray-600 mt-auto">
        <div class="flex items-center justify-between">
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Updated {{ $plugin->updated_at->diffForHumans() }}
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('plugins.show', $plugin->slug) }}" 
                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                    <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                    View Details
                </a>
                
                @if($plugin->hasVersions() && auth()->check())
                    @php
                        $latestVersion = $plugin->latestVersion;
                    @endphp
                    
                    @if($latestVersion)
                        <a href="{{ route('plugins.download.direct', ['plugin' => $plugin->slug, 'version' => $latestVersion->version]) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-150">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                            Download
                        </a>
                    @endif
                @elseif($plugin->hasVersions() && !auth()->check())
                    <button class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 cursor-not-allowed" disabled>
                        <x-heroicon-o-lock-closed class="w-4 h-4 mr-1" />
                        Login Required
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>