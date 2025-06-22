<div class="border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50" 
     x-data="{ 
         init() {
             this.$wire.on('scroll-to-reply', () => {
                 this.$el.scrollIntoView({ behavior: 'smooth' });
                 // Focus the textarea after scrolling
                 setTimeout(() => {
                     const textarea = this.$el.querySelector('textarea');
                     if (textarea) {
                         textarea.focus();
                         // Position cursor at the end
                         textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                     }
                 }, 500);
             });
         }
     }">
    @if(!$showForm)
        {{-- Reply Button --}}
        {{-- TEMPORARY: Permissions disabled for testing --}}
        {{-- @can('create', [App\Models\Post::class, $forum]) --}}
            <div class="p-4">
                <button 
                    wire:click="toggleForm"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                    Reply to Thread
                </button>
            </div>
        {{-- @else
            <div class="p-4 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @guest
                        <button wire:click="$dispatch('open-login-modal')" class="text-blue-600 dark:text-blue-400 hover:underline">
                            Log in
                        </button> to reply to this thread.
                    @else
                        You don't have permission to reply to this thread.
                    @endguest
                </p>
            </div>
        @endcan --}}
    @else
        {{-- Reply Form --}}
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Reply to Thread</h3>
                <button 
                    wire:click="toggleForm"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="createPost" class="space-y-4">
                {{-- Post Content --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Your Reply
                    </label>
                    <textarea 
                        wire:model.defer="content" 
                        id="content" 
                        rows="6"
                        placeholder="Write your reply here..."
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
                        wire:click="toggleForm"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Post Reply</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Posting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>