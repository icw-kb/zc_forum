<div>
    {{-- Upload Button --}}
    @auth
        @can('create_plugin')
            <button wire:click="$set('showModal', true)" 
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                Upload Plugin
            </button>
        @endcan
    @endauth

    {{-- Upload Modal --}}
    @if($showModal)
    <div x-data="{ open: $wire.entangle('showModal') }" 
         x-show="open" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
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
                        Upload New Plugin
                    </h3>
                    
                    {{-- Progress Steps --}}
                    <div class="mt-4">
                        <nav aria-label="Progress">
                            <ol class="flex items-center">
                                @for ($i = 1; $i <= $totalSteps; $i++)
                                    <li class="relative {{ $i < $totalSteps ? 'pr-8 sm:pr-20' : '' }} {{ $i <= $currentStep ? '' : 'opacity-50' }}">
                                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                            @if ($i < $totalSteps)
                                                <div class="h-0.5 w-full {{ $i < $currentStep ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                            @endif
                                        </div>
                                        <div class="relative w-8 h-8 flex items-center justify-center {{ $i <= $currentStep ? 'bg-blue-600 dark:bg-blue-500' : 'bg-gray-200 dark:bg-gray-700' }} rounded-full">
                                            @if ($i < $currentStep)
                                                <x-heroicon-o-check class="w-5 h-5 text-white" />
                                            @else
                                                <span class="text-xs {{ $i <= $currentStep ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}">{{ $i }}</span>
                                            @endif
                                        </div>
                                    </li>
                                @endfor
                            </ol>
                        </nav>
                    </div>

                    <form wire:submit.prevent="submit" class="mt-6">
                        {{-- Step 1: Plugin Information --}}
                        @if ($currentStep === 1)
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Plugin Name <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model.live="name" 
                                           id="name"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                                           placeholder="My Awesome Plugin">
                                    @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="slug" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        URL Slug <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="slug" 
                                           id="slug"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                                           placeholder="my-awesome-plugin">
                                    @error('slug') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Description <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <textarea wire:model="description" 
                                              id="description"
                                              rows="4"
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                                              placeholder="Describe what your plugin does..."></textarea>
                                    @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="plugin_group_id" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Category <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <select wire:model="plugin_group_id" 
                                            id="plugin_group_id"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200">
                                        <option value="">Select a category</option>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('plugin_group_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="vc_url" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                       Version Control URL <span class="text-gray-500 text-xs">(optional)</span>
                                    </label>
                                    <input type="url" 
                                           wire:model="vc_url" 
                                           id="vc_url"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                                           placeholder="https://github.com/username/repo">
                                    @error('vc_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif

                        {{-- Step 2: Version & File --}}
                        @if ($currentStep === 2)
                            <div class="space-y-4">
                                <div>
                                    <label for="version" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Version <span class="text-red-500 text-xs">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model="version" 
                                           id="version"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                                           placeholder="1.0.0">
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
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                                        {{ $version->version }}
                                                        <button type="button" 
                                                                wire:click="removeZenCartVersion({{ $versionId }})"
                                                                class="ml-2 inline-flex items-center justify-center w-4 h-4 text-blue-600 dark:text-blue-300 hover:text-blue-800 dark:hover:text-blue-100 focus:outline-none transition-colors duration-150">
                                                            <x-heroicon-o-x-mark class="w-3 h-3" />
                                                        </button>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Available versions dropdown --}}
                                    <div class="relative">
                                        <select class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
                                                onchange="if(this.value) { @this.call('addZenCartVersion', this.value); this.value = ''; }">
                                            <option value="">Select a Zen Cart version to add...</option>
                                            @foreach($zenCartVersions as $version)
                                                @if(!in_array($version->id, $selectedZenCartVersions))
                                                    <option value="{{ $version->id }}">{{ $version->version }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 italic">Select the Zen Cart versions this plugin is compatible with</p>
                                    @error('selectedZenCartVersions') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="php_version" class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">
                                        Minimum PHP Version
                                    </label>
                                    <input type="text" 
                                           wire:model="php_version" 
                                           id="php_version"
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
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
                                                <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-lg px-3 py-1 font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 transition-colors duration-200 shadow-sm border border-gray-200 dark:border-gray-600">
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
                            </div>
                        @endif


                        {{-- Form Actions --}}
                        <div class="mt-6 flex items-center justify-between">
                            <div>
                                @if ($currentStep > 1)
                                    <button type="button" 
                                            wire:click="previousStep"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                                        Previous
                                    </button>
                                @endif
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                        @click="open = false"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Cancel
                                </button>
                                
                                @if ($currentStep < $totalSteps)
                                    <button type="button" 
                                            wire:click="nextStep"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Next
                                        <x-heroicon-o-arrow-right class="w-4 h-4 ml-2" />
                                    </button>
                                @else
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <x-heroicon-o-arrow-up-tray class="w-4 h-4 mr-2" />
                                        Upload Plugin
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>