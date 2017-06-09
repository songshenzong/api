<?php

namespace Songshenzong\Api;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Songshenzong\Api\Exception\Handler;

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
        'middleware', 'transformer', 'formats',
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->prependMiddleware(Middleware::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
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
                $app['Illuminate\Contracts\Debug\ExceptionHandler'],
                env('SONGSHENZONG_API_DEBUG', env('APP_DEBUG'))
            );
        });


        $this->app->alias('SongshenzongApi', Facade::class);
        $this->app->alias('SongshenzongApi.exception', Handler::class);
    }


    /**
     * Instantiate an array of instantiable configuration values.
     *
     * @param string $item
     * @param array  $values
     *
     * @return array
     */
    protected function instantiateConfigValues($item, array $values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = $this->instantiateConfigValue($item, $value);
        }

        return $values;
    }

    /**
     * Instantiate an instantiable configuration value.
     *
     * @param string $item
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function instantiateConfigValue($item, $value)
    {
        if (is_string($value) && in_array($item, $this->instantiable, true)) {
            return $this->app->make($value);
        }

        return $value;
    }
}
