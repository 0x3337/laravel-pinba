<?php

namespace Chocofamilyme\LaravelPinba\Providers;

use Chocofamilyme\LaravelPinba\Middlewares\RightUrlMiddleware;
use Chocofamilyme\LaravelPinba\Profiler\FileDestination;
use Chocofamilyme\LaravelPinba\Profiler\NullDestination;
use Chocofamilyme\LaravelPinba\Profiler\PinbaDestination;
use Chocofamilyme\LaravelPinba\Facades\Pinba;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PinbaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge our config with application config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/pinba.php', 'pinba'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Config
        $this->publishes([
            __DIR__ . '/../config/pinba.php' => config_path('pinba.php'),
        ]);

        // Middleware
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];
        $router->middleware('pinba', RightUrlMiddleware::class);
        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware(RightUrlMiddleware::class);

        // Register pinba
        $this->app->singleton('pinba', function ($app) {
            switch (config('pinba.pinba_destination')) {
                case 'pinba':
                    if (extension_loaded('pinba') == false) {
                        Log::warning('You wanted to load Pinba destination, which is not possible due to missing pinba extension on your machine.');
                        Log::warning('If you don\'t want to see this warning again, please set PINBA_DESTINATION to null or file or install and configure pinba php extension');
                        return new NullDestination();
                    }
                    return new PinbaDestination();
                    break;

                case 'file':
                    return new FileDestination();
                    break;

                default:
                    return new NullDestination();
                    break;
            }
        });

        // Add alias to pinba
        $this->app->alias('pinba', Pinba::class);
    }
}
