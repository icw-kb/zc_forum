<div x-data="{ show: @entangle('open') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="show = false" class="bg-white dark:bg-gray-800 w-full max-w-2xl mx-4 p-6 rounded-lg shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Create New Thread</h2>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form wire:submit.prevent="createThread" class="space-y-6">
            {{-- Thread Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Thread Title
                </label>
                <input 
                    wire:model.defer="title" 
                    type="text" 
                    id="title" 
                    placeholder="Enter a descriptive title for your thread..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                />
                @error('title') 
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Thread Content --}}
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Content
                </label>
                <textarea 
                    wire:model.defer="content" 
                    id="content" 
                    rows="8"
                    placeholder="Write your message here..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 resize-vertical"
                ></textarea>
                @error('content') 
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Forum Context --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    This thread will be posted in: 
                    <span class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $forumGroup->name }} > {{ $forum->name }}
                    </span>
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button 
                    type="button" 
                    @click="show = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Create Thread</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>