<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                @if($fileAvailable)
                    Download {{ $plugin->name }}
                @else
                    {{ $plugin->name }}
                @endif
            </h1>
            <p class="text-gray-600">Version {{ $version }}</p>
        </div>

        @if($errorMessage)
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ $errorMessage }}
                        </h3>
                    </div>
                </div>
            </div>
        @endif

        @if($pluginVersion)
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Plugin Information</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="text-sm text-gray-900">{{ $plugin->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Version</dt>
                        <dd class="text-sm text-gray-900">{{ $version }}</dd>
                    </div>
                    @if($pluginVersion->file_size)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Size</dt>
                            <dd class="text-sm text-gray-900">{{ $pluginVersion->formatted_file_size }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="text-sm text-gray-900">{{ $plugin->description }}</dd>
                    </div>
                </dl>
            </div>
        @endif

        <div class="flex justify-between items-center">
            @if($fileAvailable)
                <button wire:click="download" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Download Now
                </button>
            @else
                <div class="text-red-600">File not available for download</div>
            @endif
            
            <a href="{{ route('plugins.show', $plugin->slug) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to Plugin
            </a>
        </div>
    </div>
</div>
