<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <x-heroicon-o-arrow-down-tray class="h-6 w-6 text-green-600" />
        </div>
        
        <h1 class="mt-4 text-2xl font-bold text-gray-900">Download Started</h1>
        <p class="mt-2 text-gray-600">
            Your download should begin automatically. If it doesn't, please contact support.
        </p>
        
        <div class="mt-6">
            <a href="{{ route('plugins.show', $plugin->slug) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                Back to Plugin
            </a>
        </div>
    </div>
</div>
