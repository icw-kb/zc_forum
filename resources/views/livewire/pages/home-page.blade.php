{{-- resources/views/livewire/pages/home-page.blade.php --}}

<div>
    {{-- Hero Carousel --}}
    <div class="relative w-full z-0"
         x-data="{ active: 0, total: 3 }"
         x-init="setInterval(() => { active = (active + 1) % total }, 6000)">
        <div class="relative overflow-hidden h-[700px]">
            <!-- Slide 0 -->
            <div x-show="active === 0" class="absolute inset-0 transition-opacity duration-1000">
                <div class="relative h-full">
                    <img src="/images/stock/carl-heyerdahl-KE0nC8-58MQ-unsplash.jpg" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent p-6 text-white flex items-start">
                        <div class="max-w-xl">
                            <h2 class="text-4xl md:text-5xl font-bold leading-tight">
                                Unlock limitless possibilities with Zen Cart!
                            </h2>
                            <p class="mt-4 text-base md:text-lg">
                                Revolutionize your online shopping experience with Zen Cart – the ultimate ecommerce solution that empowers you to do more.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 1 -->
            <div x-show="active === 1" class="absolute inset-0 transition-opacity duration-1000">
                <div class="relative h-full">
                    <img src="/images/stock/markus-spiske-BTKF6G-O8fU-unsplash.jpg" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent p-6 text-white flex items-start">
                        <div class="max-w-xl">
                            <h2 class="text-4xl md:text-5xl font-bold leading-tight">
                                Power your store with modern tools
                            </h2>
                            <p class="mt-4 text-base md:text-lg">
                                Zen Cart gives you the flexibility to grow fast — with powerful features and full control over your online presence.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div x-show="active === 2" class="absolute inset-0 transition-opacity duration-1000">
                <div class="relative h-full">
                    <img src="/images/stock/rupixen-Q59HmzK38eQ-unsplash.jpg" class="w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent p-6 text-white flex items-start">
                        <div class="max-w-xl">
                            <h2 class="text-4xl md:text-5xl font-bold leading-tight">
                                Built for merchants, trusted by developers
                            </h2>
                            <p class="mt-4 text-base md:text-lg">
                                Open source, battle-tested, and endlessly extendable — Zen Cart is the smart choice for serious ecommerce.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicators -->
        <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3">
            <template x-for="i in total" :key="i">
                <button
                    type="button"
                    class="w-3 h-3 rounded-full bg-white"
                    :class="{ 'bg-blue-600': active === i - 1 }"
                    @click="active = i - 1"
                ></button>
            </template>
        </div>

        <!-- Controls -->
        <button
            @click="active = (active - 1 + total) % total"
            class="absolute top-1/2 left-4 transform -translate-y-1/2 z-30 flex items-center justify-center w-12 h-12 rounded-full bg-black/50 hover:bg-black/70 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6" />
            </svg>
        </button>
        <button
            @click="active = (active + 1) % total"
            class="absolute top-1/2 right-4 transform -translate-y-1/2 z-30 flex items-center justify-center w-12 h-12 rounded-full bg-black/50 hover:bg-black/70 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 18l6-6-6-6" />
            </svg>
        </button>
    </div>

    {{-- Feature Highlights --}}
    <section class="bg-white">
        <div class="py-16 px-4 mx-auto max-w-screen-xl">
            <div class="max-w-screen-md mb-12">
                <h2 class="text-4xl font-extrabold mb-4 text-gray-900">Experience Zen Cart Today!</h2>
                <p class="text-gray-500 text-lg">Join the thousands of successful merchants who trust Zen Cart.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
                <x-feature title="Seamless Shopping Experience"
                           icon="shopping-cart"
                           description="Navigate effortlessly through a user-friendly interface." />
                <x-feature title="Customization Galore"
                           icon="adjustments"
                           description="Personalize your store with flexible options." />
                <x-feature title="Powerful Features"
                           icon="sparkles"
                           description="From inventory to payments, it's all here." />
                <x-feature title="Mobile-Optimized"
                           icon="device-mobile"
                           description="Reach customers on any device." />
                <x-feature title="Security First"
                           icon="shield-check"
                           description="Zen Cart secures your store data." />
                <x-feature title="Ongoing Support"
                           icon="support"
                           description="Access a dedicated support team anytime." />
            </div>
        </div>
    </section>
</div>
