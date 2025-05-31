<div x-data="{ show: @entangle('open') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="show = false" class="bg-white w-full max-w-md p-6 rounded shadow-lg">
        <h2 class="text-xl font-bold mb-4">Login</h2>

        <form wire:submit.prevent="login" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input wire:model.defer="email" type="email" id="email" class="w-full mt-1 border-gray-300 rounded shadow-sm" />
                @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input wire:model.defer="password" type="password" id="password" class="w-full mt-1 border-gray-300 rounded shadow-sm" />
                @error('password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.defer="remember" class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>

                <a href="#" class="text-sm text-blue-600 hover:underline" wire:click.prevent="$dispatch('open-forgot-password')">
    Forgot password?
</a>
            </div>

            <div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">
                    Login
                </button>
            </div>
        </form>
    </div>
</div>
