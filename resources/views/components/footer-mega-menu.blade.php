<footer class="bg-gray-900 text-white">
    <!-- Main Footer Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-8">
            
            <!-- Company Info Section -->
            @if($settings['show_company_info'])
            <div class="lg:col-span-2">
                <div class="mb-6">
                    <!-- Logo/Company Name -->
                    <h3 class="text-xl font-bold mb-4">Your Company</h3>
                    <p class="text-gray-300 mb-4">{{ $settings['company_description'] }}</p>
                </div>
                
                <!-- Contact Information -->
                <div class="space-y-2 text-sm text-gray-300">
                    <p class="flex items-center">
                        <x-heroicon-o-map-pin class="w-4 h-4 mr-2" />
                        {{ $contactInfo['address'] }}
                    </p>
                    <p class="flex items-center">
                        <x-heroicon-o-phone class="w-4 h-4 mr-2" />
                        {{ $contactInfo['phone'] }}
                    </p>
                    <p class="flex items-center">
                        <x-heroicon-o-envelope class="w-4 h-4 mr-2" />
                        {{ $contactInfo['email'] }}
                    </p>
                </div>
            </div>
            @endif

            <!-- Menu Sections -->
            @foreach($sections as $sectionKey => $section)
                <div class="lg:col-span-1">
                    <h4 class="text-lg font-semibold mb-4">{{ $section['title'] }}</h4>
                    <ul class="space-y-2">
                        @foreach($section['links'] as $link)
                            <li>
                                <a href="{{ $link['url'] }}" 
                                   @if($link['external']) target="_blank" rel="noopener noreferrer" @endif
                                   class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center text-sm">
                                    @if(isset($link['icon']))
                                        <x-dynamic-component :component="$link['icon']" class="w-4 h-4 mr-2" />
                                    @endif
                                    {{ $link['label'] }}
                                    @if($link['external'])
                                        <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3 ml-1" />
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <!-- Newsletter Signup -->
            @if($settings['show_newsletter'])
            <div class="lg:col-span-2 lg:col-start-5">
                <h4 class="text-lg font-semibold mb-4">Stay Updated</h4>
                <p class="text-gray-300 mb-4 text-sm">Subscribe to our newsletter for updates and insights.</p>
                
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <input type="email" 
                               name="email" 
                               placeholder="Enter your email"
                               class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                        Subscribe
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">{{ $settings['copyright_text'] }}</p>
                
                <!-- Additional bottom links if needed -->
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="/sitemap" class="text-gray-400 hover:text-white text-sm transition-colors">Sitemap</a>
                    <a href="/accessibility" class="text-gray-400 hover:text-white text-sm transition-colors">Accessibility</a>
                </div>
            </div>
        </div>
    </div>
</footer>