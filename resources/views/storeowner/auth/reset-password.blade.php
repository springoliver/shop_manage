<x-guest-layout>
    <div class="w-full max-w-md bg-white/95 rounded-lg shadow-lg p-8">
        <form method="POST" action="{{ route('storeowner.password.store') }}">
            @csrf
            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <!-- Email Address -->
            <div>
                <x-input-label for="emailid" :value="__('Email')" />
                <x-text-input id="emailid" class="block mt-1 w-full" type="email" name="emailid" :value="old('emailid', $request->emailid)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('emailid')" class="mt-2" />
            </div>
            <!-- Password -->
            <div class="mt-4 relative">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full pr-16" type="password" name="password" required autocomplete="new-password" />
                <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-9 text-sm text-gray-600 focus:outline-none">Show</button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <!-- Confirm Password -->
            <div class="mt-4 relative">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-16" type="password" name="password_confirmation" required autocomplete="new-password" />
                <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 top-9 text-sm text-gray-600 focus:outline-none">Show</button>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    <script>
    function togglePassword(id, btn) {
        const passwordInput = document.getElementById(id);
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

