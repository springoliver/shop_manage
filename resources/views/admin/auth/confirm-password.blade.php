
<x-guest-layout>
    <div class="w-full max-w-md bg-white/95 rounded-lg shadow-lg p-8">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>
        <form method="POST" action="{{ route('admin.password.confirm') }}">
            @csrf
            <!-- Password -->
            <div class="relative">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full pr-16" type="password" name="password" required autocomplete="current-password" />
                <button type="button" onclick="togglePassword()" class="absolute right-3 top-9 text-sm text-gray-600 focus:outline-none">Show</button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div class="flex justify-end mt-4">
                <x-primary-button>
                    {{ __('Confirm') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const btn = event.currentTarget;
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            btn.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            btn.textContent = 'Show';
        }
    }
    </script>
</x-guest-layout>
