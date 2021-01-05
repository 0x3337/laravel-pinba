<?php

namespace Chocofamilyme\LaravelPinba\Providers;

use Chocofamilyme\LaravelPinba\Listeners\ProfileJob;
use Chocofamilyme\LaravelPinba\Listeners\ProfileStartCommand;
use Chocofamilyme\LaravelPinba\Middlewares\RightUrlMiddleware;
use Chocofamilyme\LaravelPinba\Profiler\FileDestination;
use Chocofamilyme\LaravelPinba\Profiler\NullDestination;
use Chocofamilyme\LaravelPinba\Profiler\PinbaDestination;
use Chocofamilyme\LaravelPinba\Facades\Pinba;
use Chocofamilyme\LaravelPinba\Profiler\ProfilerInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
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
            __DIR__ . '/../config/pinba.php',
            'pinba'
        );
    }

    /**
     * Bootstrap services.
     *
     * @psalm-suppress UndefinedFunction
     * @psalm-suppress UndefinedInterfaceMethod
     * @psalm-suppress UndefinedDocblockClass
     * @return void
     */
    public function boot()
    {
        // Config
        $this->publishes(
            [
                __DIR__ . '/../config/pinba.php' => $this->app->configPath('pinba.php'),
            ]
        );

        $this->init();

        // Register pinba
        $this->app->singleton(
            ProfilerInterface::class,
            function (Application $app) {
                /** @var Repository $config */
                $config = $app->get('config');

                switch ($config->get('pinba.pinba_destination')) {
                    case 'pinba':
                        if (extension_loaded('pinba') === false) {
                            Log::warning(
                                'You wanted to load Pinba destination, which is not possible due to missing pinba extension on your machine.'
                            );
                            Log::warning(
                                'If you don\'t want to see this warning again, please set PINBA_DESTINATION to null or file or install and configure pinba php extension'
                            );

                            return new NullDestination();
                        }

                        $schema = $config->get('pinba.pinba_schema');
                        if (null === $schema) {
                            $config->set('pinba.pinba_schema', $app->runningInConsole() ? 'console' : 'web');
                        }

                        return new PinbaDestination($config);
                        break;

                    case 'file':
                        return new FileDestination();
                        break;

                    default:
                        return new NullDestination();
                        break;
                }
            }
        );

        // Add alias to pinba
        $this->app->alias(ProfilerInterface::class, Pinba::class);
    }

    private function init(): void
    {
        if ($this->app->runningInConsole()) {
            $this->initListeners();
        } else {
            $this->initMiddleware();
        }
    }

    /** @psalm-suppress UndefinedClass */
    private function initListeners(): void
    {
        Event::listen(
            \Illuminate\Console\Events\CommandStarting::class,
            ProfileStartCommand::class
        );

        Event::listen(
            \Illuminate\Queue\Events\JobProcessing::class,
            ProfileJob::class
        );

        Event::listen(
            \Illuminate\Queue\Events\JobProcessed::class,
            ProfileJob::class
        );

        Event::listen(
            \Illuminate\Queue\Events\JobFailed::class,
            ProfileJob::class
        );
    }

    /** @psalm-suppress UndefinedDocblockClass */
    private function initMiddleware(): void
    {
        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app->get('router');
        $router->middleware(RightUrlMiddleware::class);

        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app->get('Illuminate\Contracts\Http\Kernel');
        $kernel->pushMiddleware(RightUrlMiddleware::class);
    }
}
