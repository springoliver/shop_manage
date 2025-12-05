<x-guest-layout>
    <div class="w-full max-w-md bg-white/95 rounded-lg shadow-lg p-8">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('storeowner.password.email') }}">
            @csrf
            <!-- Email Address -->
            <div>
                <x-input-label for="emailid" :value="__('Email')" />
                <x-text-input id="emailid" class="block mt-1 w-full" type="email" name="emailid" :value="old('emailid')" required autofocus />
                <x-input-error :messages="$errors->get('emailid')" class="mt-2" />
            </div>
            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

