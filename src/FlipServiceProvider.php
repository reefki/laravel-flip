<?php

namespace Reefki\Flip;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;

class FlipServiceProvider extends ServiceProvider
{
    /**
     * Register package services into the Laravel container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/flip.php', 'flip');

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                $app->make(HttpFactory::class),
                $app['config']->get('flip', []),
            );
        });

        $this->app->singleton('flip', fn ($app) => new Flip($app->make(Client::class)));
        $this->app->alias('flip', Flip::class);
    }

    /**
     * Bootstrap publishables when running in the console.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/flip.php' => $this->app->configPath('flip.php'),
            ], 'flip-config');
        }
    }

    /**
     * Container bindings provided by this service provider (deferred).
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['flip', Flip::class, Client::class];
    }
}
