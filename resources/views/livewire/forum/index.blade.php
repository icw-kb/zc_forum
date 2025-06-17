<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Forums</h1>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">
            Join the discussion and connect with the Zen Cart community.
        </p>
    </div>

    {{-- Forum Groups --}}
    <div class="space-y-8">
        @foreach($forumGroups as $forumGroup)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Forum Group Header --}}
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $forumGroup->name }}
                    </h2>
                    @if($forumGroup->description)
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $forumGroup->description }}
                        </p>
                    @endif
                </div>

                {{-- Forums in Group --}}
                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($forumGroup->forums as $forum)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-start space-x-4">
                                    {{-- Forum Icon --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Forum Info --}}
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('forums.show', [$forumGroup->slug, $forum->slug]) }}" 
                                           class="text-lg font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $forum->name }}
                                        </a>
                                        @if($forum->description)
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $forum->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Forum Stats --}}
                                <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="text-center">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $forum->threads_count ?? 0 }}</div>
                                        <div>Threads</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $forum->posts_count ?? 0 }}</div>
                                        <div>Posts</div>
                                    </div>
                                    @if($forum->latestPost)
                                        <div class="text-center min-w-0">
                                            <div class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $forum->latestPost->user->name }}
                                            </div>
                                            <div class="truncate">{{ $forum->latestPost->created_at->diffForHumans() }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @if($forumGroups->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No forums available</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Forums will appear here once they are created.</p>
        </div>
    @endif
</div>