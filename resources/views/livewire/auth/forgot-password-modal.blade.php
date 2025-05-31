<div x-data="{ show: @entangle('open') }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div @click.away="show = false" class="bg-white w-full max-w-md p-6 rounded shadow-lg">
        <h2 class="text-xl font-bold mb-4">Forgot Password</h2>

        <form wire:submit.prevent="sendResetLink" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <input wire:model.defer="email" type="email" id="email" class="w-full mt-1 border-gray-300 rounded shadow-sm" />
                @error('email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700">
                    Send Reset Link
                </button>
            </div>
        </form>
    </div>
</div>
