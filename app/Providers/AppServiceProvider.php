<?php

namespace App\Providers;

use App\Auth\CustomPasswordBrokerManager;
use App\Auth\StoreOwnerUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Override password broker manager by setting instance directly
        // This ensures our custom manager overrides the framework default
        $manager = new CustomPasswordBrokerManager($this->app);
        $this->app->instance('auth.password', $manager);
        $this->app->instance(\Illuminate\Contracts\Auth\PasswordBrokerFactory::class, $manager);
        
        // Clear facade cache to ensure it uses the new instance
        \Illuminate\Support\Facades\Password::clearResolvedInstance('auth.password');

        // Register custom user provider for storeowners
        Auth::provider('storeowner', function ($app, array $config) {
            return new StoreOwnerUserProvider(
                $app['hash'],
                $config['model']
            );
        });
    }

}
