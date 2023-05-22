<?php

namespace App\Services\Authentication;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AuthenticationServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bind AuthenticationServiceInterface to laravel Service Container
     * Defer its registration until one of the registered bindings is actually needed
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationServiceInterface::class, function (Application $app) {
            return new AuthenticationService();
        });
    }

    /**
     * return  the service container bindings registered by the provider
     *
     * @return string[]
     */
    public function provides(): array
    {
        return [AuthenticationServiceInterface::class];
    }
}
