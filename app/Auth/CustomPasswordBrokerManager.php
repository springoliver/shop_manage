<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\PasswordBrokerManager as BasePasswordBrokerManager;

class CustomPasswordBrokerManager extends BasePasswordBrokerManager
{
    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config)
    {
        $table = $config['table'] ?? null;
        
        // Use custom token repository for storeowners broker
        if ($table === 'storeowner_password_reset_tokens') {
            $key = $this->app['config']['app.key'];

            if (str_starts_with($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            return new StoreOwnerTokenRepository(
                $this->app['db']->connection($config['connection'] ?? null),
                $this->app['hash'],
                $table,
                $key,
                $config['expire'] ?? 60,
                $config['throttle'] ?? 60
            );
        }

        return parent::createTokenRepository($config);
    }
}

