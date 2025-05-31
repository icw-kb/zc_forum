<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-6 rounded shadow max-w-md w-full text-center">
        <h2 class="text-2xl font-semibold mb-4">Verify Your Email Address</h2>
        <p class="mb-4">
            Thanks for signing up! Please verify your email address by clicking the link we just emailed to you.
            If you didn’t receive the email, we will gladly send you another.
        </p>

        @if (session('status'))
            <div class="text-green-600 font-medium mb-4">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit.prevent="resend">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Resend Verification Email
            </button>
        </form>
    </div>
</div>
