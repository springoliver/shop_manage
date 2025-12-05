
<x-guest-layout>
    <div class="w-full max-w-md bg-white/95 rounded-lg shadow-lg p-8">
        <h2 class="text-center mb-6 font-medium text-lg">Enter Email and password to continue.</h2>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4 relative">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full pr-16" type="password" name="password" required autocomplete="current-password" />
                <button type="button" onclick="togglePassword()" class="absolute right-3 top-9 text-sm text-gray-600 focus:outline-none">Show</button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('admin.password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('admin.password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3">
                    {{ __('Log in') }}
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
