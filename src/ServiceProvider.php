<?php

namespace Songshenzong\Api;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Songshenzong\Api\Exception\Handler;
use function config;

/**
 * Class ServiceProvider
 *
 * @package Songshenzong\Api
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Array of config items that are instantiable.
     *
     * @var array
     */
    protected $instantiable = [
        'middleware',
        'transformer',
        'formats',
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->prependMiddleware(Middleware::class);
        $this->publishes([
                             __DIR__ . '/../config/api.php' => config_path('api.php'),
                         ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('SongshenzongApi', function ($app) {
            return new Api(
                $app,
                $app[Handler::class],
                $app[Router::class]
            );
        });


        $this->app->singleton('SongshenzongApi.exception', function ($app) {
            return new Handler(
                $app[ExceptionHandler::class],
                config('api.debug')
            );
        });


        $this->app->alias('SongshenzongApi', Facade::class);
        $this->app->alias('SongshenzongApi.exception', Handler::class);
    }

}
