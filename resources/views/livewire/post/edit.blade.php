<div>
    @if($showForm)
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Edit Post</h4>
            
            <form wire:submit.prevent="updatePost" class="space-y-4">
                {{-- Post Content --}}
                <div>
                    <textarea 
                        wire:model.defer="content" 
                        rows="6"
                        placeholder="Edit your post content..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-gray-100 resize-vertical"
                    ></textarea>
                    @error('content') 
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end space-x-3">
                    <button 
                        type="button" 
                        wire:click="cancelEdit"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-1.5 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Update Post</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>