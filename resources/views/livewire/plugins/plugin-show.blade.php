<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li>
                <a href="{{ route('plugins.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    <x-heroicon-o-home class="flex-shrink-0 h-5 w-5" />
                    <span class="sr-only">Plugins</span>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <x-heroicon-o-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400 dark:text-gray-500" />
                    <a href="{{ route('plugins.index') }}" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        Plugins
                    </a>
                </div>
            </li>
            @if($plugin->group)
                <li>
                    <div class="flex items-center">
                        <x-heroicon-o-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400 dark:text-gray-500" />
                        <a href="{{ route('plugins.group', $plugin->group->slug) }}" class="ml-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            {{ $plugin->group->name }}
                        </a>
                    </div>
                </li>
            @endif
            <li>
                <div class="flex items-center">
                    <x-heroicon-o-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400 dark:text-gray-500" />
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $plugin->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    
    {{-- Plugin Header --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3 mb-4">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 break-words">{{ $plugin->name }}</h1>
                        
                        @if($plugin->featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <x-heroicon-s-star class="w-4 h-4 mr-1" />
                                Featured
                            </span>
                        @endif
                        
                        @if($plugin->group)
                            <a href="{{ route('plugins.group', $plugin->group->slug) }}" 
                               class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800">
                                {{ $plugin->group->name }}
                            </a>
                        @endif
                    </div>
                    
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">{{ $plugin->description }}</p>
                    
                    {{-- Plugin Stats --}}
                    <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-sm text-gray-500 dark:text-gray-400">
                        <span class="flex items-center">
                            <x-heroicon-o-eye class="w-5 h-5 mr-2" />
                            {{ number_format($plugin->view_count) }} views
                        </span>
                        <span class="flex items-center">
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                            {{ number_format($plugin->download_count) }} downloads
                        </span>
                        <span class="flex items-center">
                            <x-heroicon-o-clock class="w-5 h-5 mr-2" />
                            Updated {{ $plugin->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
                
                {{-- Quick Actions --}}
                <div class="flex flex-col sm:flex-row lg:flex-col space-y-3 sm:space-y-0 sm:space-x-3 lg:space-x-0 lg:space-y-3 ml-0 sm:ml-6 mt-4 sm:mt-0">
                    @if($canDownload && $plugin->latestVersion)
                        <a href="{{ route('plugins.download.direct', [$plugin->slug, $plugin->latestVersion->version]) }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm hover:shadow-md transition-all duration-150">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                            Download Latest
                        </a>
                    @elseif(!auth()->check())
                        <button wire:click="$dispatch('open-login-modal')" 
                                class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                            <x-heroicon-o-lock-closed class="w-4 h-4 mr-2" />
                            Login to Download
                        </button>
                    @endif
                    
                    @if($plugin->github_url)
                        <a href="{{ $plugin->github_url }}" 
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150">
                            <x-heroicon-o-code-bracket class="w-4 h-4 mr-2" />
                            View Source
                            <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3 ml-1" />
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Version Downloads --}}
            <div id="download">
                <x-plugins.version-list 
                    :versions="$versions" 
                    :plugin="$plugin" 
                    :canDownload="$canDownload" 
                />
            </div>
            
            {{-- Additional Plugin Information --}}
            @if($plugin->latestVersion)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Plugin Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Latest Version</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $plugin->latestVersion->version }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Release Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $plugin->latestVersion->created_at->format('M j, Y') }}</dd>
                            </div>
                            
                            @if($plugin->latestVersion->file_size)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">File Size</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $plugin->latestVersion->formatted_file_size }}</dd>
                                </div>
                            @endif
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $plugin->status === 'active' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                                        {{ ucfirst($plugin->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- Sidebar --}}
        <div class="space-y-6 lg:space-y-8">
            {{-- Compatible Zen Cart Versions --}}
            @if($plugin->latestVersion && $plugin->latestVersion->zencartVersions->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Compatibility</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Zen Cart Versions</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($plugin->latestVersion->zencartVersions as $version)
                                        <span class="inline-block bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-sm px-3 py-1 rounded-full">
                                            {{ $version->version }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            {{-- Plugin Statistics --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Statistics</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Views</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($plugin->view_count) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Downloads</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($plugin->download_count) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Versions Available</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $versions->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Related Plugins --}}
            @if($relatedPlugins->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Related Plugins</h3>
                        @if($plugin->group)
                            <p class="text-sm text-gray-500 dark:text-gray-400">More plugins in {{ $plugin->group->name }}</p>
                        @endif
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($relatedPlugins as $relatedPlugin)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                            <a href="{{ route('plugins.show', $relatedPlugin->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                                                {{ $relatedPlugin->name }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">{{ $relatedPlugin->description ?: 'No description available.' }}</p>
                                        <div class="flex items-center space-x-4 mt-2 text-xs text-gray-400 dark:text-gray-500">
                                            <span class="flex items-center">
                                                <x-heroicon-o-arrow-down-tray class="w-3 h-3 mr-1" />
                                                {{ number_format($relatedPlugin->download_count) }} downloads
                                            </span>
                                            @if($relatedPlugin->latestVersion)
                                                <span class="bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded text-gray-700 dark:text-gray-300">
                                                    v{{ $relatedPlugin->latestVersion->version }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($plugin->group)
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t dark:border-gray-600">
                            <a href="{{ route('plugins.group', $plugin->group->slug) }}" 
                               class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                                View all {{ $plugin->group->name }} plugins →
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
