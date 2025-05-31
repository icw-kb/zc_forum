{{-- resources/views/livewire/components/toast-notifications.blade.php --}}

<div class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full">
    @foreach($notifications as $notification)
        <div x-data="{ 
                show: true,
                id: '{{ $notification['id'] }}',
                autoRemove() {
                    setTimeout(() => {
                        this.show = false;
                        setTimeout(() => {
                            $wire.removeNotification(this.id);
                        }, 300);
                    }, {{ $notification['duration'] }});
                }
            }"
            x-init="autoRemove()"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full scale-95"
            x-transition:enter-end="opacity-100 transform translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 transform translate-x-full scale-95"
            class="pointer-events-auto">
            
            <div class="rounded-lg shadow-lg overflow-hidden
                @switch($notification['type'])
                    @case('success')
                        bg-green-500
                        @break
                    @case('error')
                        bg-red-500
                        @break
                    @case('warning')
                        bg-yellow-500
                        @break
                    @case('info')
                        bg-blue-500
                        @break
                    @default
                        bg-gray-500
                @endswitch">
                
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @switch($notification['type'])
                                @case('success')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    @break
                                @case('error')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    @break
                                @case('warning')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    @break
                                @case('info')
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @break
                            @endswitch
                        </div>
                        
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium text-white">
                                {{ $notification['message'] }}
                            </p>
                        </div>
                        
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="show = false; setTimeout(() => $wire.removeNotification(id), 300)"
                                    class="inline-flex text-white hover:text-gray-200 focus:outline-none focus:text-gray-200 transition ease-in-out duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>