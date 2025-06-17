<div class="flex items-center space-x-2">
    <button 
        wire:click="toggleLike"
        class="inline-flex items-center px-2 py-1 text-sm rounded transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
            {{ $isLiked 
                ? 'text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30' 
                : 'text-gray-500 hover:text-red-600 hover:bg-red-50 dark:text-gray-400 dark:hover:text-red-400 dark:hover:bg-red-900/20' }}"
    >
        <svg class="w-4 h-4 mr-1" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        
        <span wire:loading.remove wire:target="toggleLike">
            {{ $isLiked ? 'Liked' : 'Like' }}
        </span>
        
        <span wire:loading wire:target="toggleLike" class="flex items-center">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    </button>
    
    @if($likesCount > 0)
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $likesCount }} {{ Str::plural('like', $likesCount) }}
        </span>
    @endif
</div>