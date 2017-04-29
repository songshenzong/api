<?php

namespace Songshenzong\Api;

use Illuminate\Routing\Router;
use Songshenzong\Api\Exception\Handler;
use Illuminate\Contracts\Http\Kernel;

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
        // Response ::setFormatters($this -> config('formats'));
        // Request ::setAcceptParser($this -> app['Songshenzong\Api\Http\Parser\Accept']);
        $kernel = $this -> app -> make(Kernel::class);
        $kernel -> prependMiddleware(Middleware::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // $this -> app -> singleton(AcceptParser::class, function ($app) {
        //     return new AcceptParser(
        //         'x',
        //         '',
        //         'v1',
        //         'json'
        //     );
        // });


        $this -> app -> singleton('SongshenzongApi', function ($app) {
            return new Api(
                $app,
                $app[Handler::class],
                $app[Router::class]
            );
        });


        $this -> app -> singleton('SongshenzongApi.exception', function ($app) {
            return new Handler(
                $app['Illuminate\Contracts\Debug\ExceptionHandler'],
                env('SONGSHENZONG_API_DEBUG', env('APP_DEBUG'))
            );
        });


        $this -> app -> alias('SongshenzongApi', 'Songshenzong\Api\Facade');
        $this -> app -> alias('SongshenzongApi.exception', 'Songshenzong\Api\Exception\Handler');
    }


    /**
     * Retrieve and instantiate a config value if it exists and is a class.
     *
     * @param      $item
     * @param bool $instantiate
     *
     * @return array|mixed
     */
    // protected function config($item, $instantiate = true)
    // {
    //     $value = $this -> app['config'] -> get('api.' . $item);
    //
    //     if (is_array($value)) {
    //         return $instantiate ? $this -> instantiateConfigValues($item, $value) : $value;
    //     }
    //
    //     return $instantiate ? $this -> instantiateConfigValue($item, $value) : $value;
    // }

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
            $values[$key] = $this -> instantiateConfigValue($item, $value);
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
        if (is_string($value) && in_array($item, $this -> instantiable)) {
            return $this -> app -> make($value);
        }

        return $value;
    }
}
