<div>
    {{-- Upload Version Button --}}
    @if($this->canUploadVersion())
        <button wire:click="$set('showModal', true)" 
                class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <x-heroicon-o-plus class="w-4 h-4 mr-2" />
            Upload New Version
        </button>
    @endif

    {{-- Upload Modal --}}
    <div x-data="{ open: @entangle('showModal') }" 
         x-show="open" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            {{-- Background overlay --}}
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity bg-gray-500/50 dark:bg-gray-900/50"></div>

            {{-- Modal panel --}}
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="open = false" 
                            type="button" 
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        Upload New Version for "{{ $plugin->name }}"
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Category: {{ $plugin->group->name ?? 'Uncategorized' }}
                    </p>

                    <form wire:submit.prevent="submit" class="mt-6">
                        <div class="space-y-4">
                                <div>
                                    <label for="version" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Version Number <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model.live="version" 
                                           id="version"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                                           placeholder="1.1.0">
                                    @error('version') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Compatible Zen Cart Versions <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    
                                    {{-- Selected versions (pills) --}}
                                    @if(count($selectedZenCartVersions) > 0)
                                        <div class="mb-3 flex flex-wrap gap-2">
                                            @foreach($selectedZenCartVersions as $versionId)
                                                @php
                                                    $version = $zenCartVersions->firstWhere('id', $versionId);
                                                @endphp
                                                @if($version)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                                                        {{ $version->version }}
                                                        <button type="button" 
                                                                wire:click="removeZenCartVersion({{ $versionId }})"
                                                                class="ml-2 inline-flex items-center justify-center w-4 h-4 text-green-600 dark:text-green-300 hover:text-green-800 dark:hover:text-green-100 focus:outline-none transition-colors duration-150">
                                                            <x-heroicon-o-x-mark class="w-3 h-3" />
                                                        </button>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Available versions dropdown --}}
                                    <div class="relative">
                                        <select class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                                                onchange="if(this.value) { @this.call('addZenCartVersion', this.value); this.value = ''; }">
                                            <option value="">Select a Zen Cart version to add...</option>
                                            @foreach($zenCartVersions as $version)
                                                @if(!in_array($version->id, $selectedZenCartVersions))
                                                    <option value="{{ $version->id }}">{{ $version->version }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 italic">Select the Zen Cart versions this version is compatible with</p>
                                    @error('selectedZenCartVersions') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="php_version" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Minimum PHP Version
                                    </label>
                                    <input type="text" 
                                           wire:model="php_version" 
                                           id="php_version"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                                           placeholder="7.4">
                                    @error('php_version') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Plugin File (ZIP) <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <div class="mt-1 flex justify-center px-6 pt-6 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-750 transition-colors duration-200">
                                        <div class="space-y-2 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex justify-center text-sm text-gray-600 dark:text-gray-400">
                                                <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-lg px-3 py-1 font-medium text-green-600 dark:text-green-400 hover:text-green-500 dark:hover:text-green-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500 transition-colors duration-200 shadow-sm border border-gray-200 dark:border-gray-600">
                                                    <span>Choose file</span>
                                                    <input id="file-upload" wire:model="uploadedFile" type="file" class="sr-only" accept=".zip">
                                                </label>
                                                <p class="pl-2 self-center">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">ZIP files up to 10MB</p>
                                        </div>
                                    </div>
                                    @if ($uploadedFile)
                                        <div class="mt-3 flex items-center space-x-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                            <x-heroicon-o-document-arrow-up class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            <p class="text-sm text-green-700 dark:text-green-300 font-medium">
                                                {{ $uploadedFile->getClientOriginalName() }}
                                            </p>
                                        </div>
                                    @endif
                                    @error('uploadedFile') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Version Description / Changelog <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <textarea wire:model="description" 
                                              id="description"
                                              rows="4"
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-200"
                                              placeholder="What changed in this version? Bug fixes, new features, improvements..."></textarea>
                                    @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Describe what's new or changed in this version. This will be shown to users.
                                    </p>
                                </div>
                            </div>

                        {{-- Form Actions --}}
                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <button type="button" 
                                    @click="open = false"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </button>
                            
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <x-heroicon-o-arrow-up-tray class="w-4 h-4 mr-2" />
                                Upload Version
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>