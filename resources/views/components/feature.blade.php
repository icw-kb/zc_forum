@props(['title', 'description', 'icon'])

<div>
    <div class="flex items-center justify-center mb-4 w-12 h-12 rounded-full bg-blue-100">
        <x-heroicon-o-{{ $icon }} class="w-6 h-6 text-blue-600" />
    </div>
    <h3 class="mb-2 text-xl font-bold">{{ $title }}</h3>
    <p class="text-gray-500">{{ $description }}</p>
</div>
