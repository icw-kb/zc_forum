@props(['versions', 'plugin', 'canDownload' => false])

<div class="bg-white rounded-lg shadow-sm border">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Available Versions</h3>
        <p class="mt-1 text-sm text-gray-500">
            Download previous versions of {{ $plugin->name }}
        </p>
    </div>
    
    @if($versions->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($versions as $version)
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium 
                                        {{ $loop->first ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        v{{ $version->version }}
                                        @if($loop->first)
                                            <span class="ml-1 text-xs">(Latest)</span>
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="min-w-0 flex-1">
                                    @if($version->description)
                                        <p class="text-sm text-gray-900 mb-1">{{ $version->description }}</p>
                                    @endif
                                    
                                    <div class="flex items-center text-xs text-gray-500 space-x-4">
                                        <span>Released {{ $version->created_at->format('M j, Y') }}</span>
                                        
                                        @if($version->file_size)
                                            <span>{{ $version->formatted_file_size }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Compatible Zen Cart Versions --}}
                            @if($version->zencartVersions->count() > 0)
                                <div class="mt-3">
                                    <div class="text-xs text-gray-500 mb-1">Compatible with Zen Cart:</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($version->zencartVersions as $zcVersion)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                                {{ $zcVersion->version }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Download Actions --}}
                        <div class="flex items-center space-x-3">
                            @if($canDownload && $version->hasFile())
                                <a href="{{ route('plugins.download', [$plugin->slug, $version->version]) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                                    Download
                                </a>
                            @elseif($canDownload && !$version->hasFile())
                                <button disabled 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    <x-heroicon-o-x-circle class="w-4 h-4 mr-1" />
                                    No File
                                </button>
                            @else
                                <div class="text-center">
                                    <button disabled 
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                        <x-heroicon-o-lock-closed class="w-4 h-4 mr-1" />
                                        Login Required
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <a href="#" wire:click="$dispatch('open-login-modal')" class="text-blue-600 hover:text-blue-500">
                                            Sign in to download
                                        </a>
                                    </p>
                                </div>
                            @endif
                            
                            {{-- Additional Actions --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        type="button"
                                        class="inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <x-heroicon-o-ellipsis-vertical class="w-4 h-4" />
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                                     style="display: none;">
                                    <div class="py-1">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            View Changelog
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Report Issue
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="px-6 py-8 text-center">
            <x-heroicon-o-folder-open class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No versions available</h3>
            <p class="mt-1 text-sm text-gray-500">
                This plugin doesn't have any downloadable versions yet.
            </p>
        </div>
    @endif
</div>