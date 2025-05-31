<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white shadow rounded">
        <h2 class="text-xl font-semibold mb-4 text-center">Reset Password</h2>

        <form wire:submit.prevent="resetPassword" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model.defer="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" required />
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" wire:model.defer="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" required />
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" wire:model.defer="password_confirmation" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" required />
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                Reset Password
            </button>
        </form>
    </div>
</div>
