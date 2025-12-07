<?php

namespace App\Http\StoreOwner\Controllers\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emailid' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('emailid', 'password');
        
        // Manually authenticate to check status
        $storeOwner = \App\Models\StoreOwner::where('emailid', $credentials['emailid'])->first();
        
        if (!$storeOwner) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'emailid' => trans('auth.failed'),
            ]);
        }

        // Check password - support both Laravel Hash and base64 (for backward compatibility with CI)
        $passwordValid = false;
        if (\Illuminate\Support\Facades\Hash::check($credentials['password'], $storeOwner->password)) {
            $passwordValid = true;
        } elseif (base64_encode($credentials['password']) === $storeOwner->password) {
            // Backward compatibility: if password matches base64, rehash it with Laravel Hash
            // Use setRawAttributes to bypass the 'hashed' cast, then save
            $storeOwner->setRawAttributes(array_merge($storeOwner->getAttributes(), [
                'password' => \Illuminate\Support\Facades\Hash::make($credentials['password'])
            ]));
            $storeOwner->save();
            $passwordValid = true;
        }

        if (!$passwordValid) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'emailid' => trans('auth.failed'),
            ]);
        }

        // Check owner status
        if ($storeOwner->status !== 'Active') {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'emailid' => 'You have not activated your account yet. Please activate your account to login.',
            ]);
        }

        // Check store status - owner must have at least one active store
        $activeStore = \App\Models\Store::where('storeownerid', $storeOwner->ownerid)
            ->where('status', 'Active')
            ->first();

        if (!$activeStore) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'emailid' => 'Your store is not verified. Please contact admin.',
            ]);
        }

        // Login the user
        Auth::guard('storeowner')->login($storeOwner, $this->boolean('remember'));
        
        // Store storeid in session
        session(['storeid' => $activeStore->storeid]);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'emailid' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('emailid')).'|'.$this->ip().'|storeowner');
    }
}

