<?php

namespace App\Services\Events;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bind EventServiceInterface to laravel Service Container
     * Defer its registration until one of the registered bindings is actually needed
     */
    public function register(): void
    {
        $this->app->bind(EventServiceInterface::class, function (Application $app) {
            return new EventService();
        });
    }

    /**
     * return  the service container bindings registered by the provider
     *
     * @return string[]
     */
    public function provides(): array
    {
        return [EventServiceInterface::class];
    }
}
