<?php

namespace Songshenzong\ResponseJson;

use Songshenzong\ResponseJson\Exception\Handler as ExceptionHandler;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use RuntimeException;
use Songshenzong\ResponseJson\Http\Request;
use Songshenzong\ResponseJson\Http\Response;
use Songshenzong\ResponseJson\Contract\Routing\Adapter;

use Songshenzong\ResponseJson\Http\Parser\Accept as AcceptParser;

use ReflectionClass;
use Illuminate\Contracts\Http\Kernel;
use Songshenzong\ResponseJson\Routing\Adapter\Laravel;

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
        'middleware', 'auth', 'throttling', 'transformer', 'formats',
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {

        Response ::setFormatters($this -> config('formats'));
        Request ::setAcceptParser($this -> app['Songshenzong\ResponseJson\Http\Parser\Accept']);
        $kernel = $this -> app -> make(Kernel::class);
        $kernel -> prependMiddleware(ResponseJson::class);

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        /**---------------------------------------------------------
         *   Class
         *---------------------------------------------------------*/

        $aliases = [
            'responseJson.router.adapter' => 'Songshenzong\ResponseJson\Contract\Routing\Adapter',
            'responseJson.exception'      => ['Songshenzong\ResponseJson\Exception\Handler', 'Songshenzong\ResponseJson\Contract\Debug\ExceptionHandler'],
        ];

        foreach ($aliases as $key => $aliases) {
            foreach ((array)$aliases as $alias) {
                $this -> app -> alias($key, $alias);
            }
        }


        $this -> app -> singleton(AcceptParser::class, function ($app) {
            return new AcceptParser(
                'x',
                '',
                'v1',
                'json'
            );
        });


        $this -> app -> singleton('responseJson.exception', function ($app) {
            return new ExceptionHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], $this -> config('debug'));
        });


        $this -> app -> singleton('responseJson.router.adapter', function ($app) {
            return new Laravel($app['router']);
        });


        $this -> app -> singleton('ResponseJson', function ($app) {
            return new ResponseJson(
                $app,
                $app[ExceptionHandler::class],
                $app[Adapter::class]
            );
        });


        $this -> registerClassAliases();
        $this -> registerExceptionHandler();
    }


    /**
     * Retrieve and instantiate a config value if it exists and is a class.
     *
     * @param string $key
     * @param bool   $instantiate
     *
     * @return mixed
     */
    protected function config($item, $instantiate = true)
    {
        $value = $this -> app['config'] -> get('api.' . $item);

        if (is_array($value)) {
            return $instantiate ? $this -> instantiateConfigValues($item, $value) : $value;
        }

        return $instantiate ? $this -> instantiateConfigValue($item, $value) : $value;
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

    /**
     * Register the class aliases.
     *
     * @return void
     */
    protected function registerClassAliases()
    {

        $this -> app -> alias('ResponseJson', 'Songshenzong\ResponseJson\Facade');

        $aliases = [
            'responseJson.exception' => ['Songshenzong\ResponseJson\Exception\Handler', 'Songshenzong\ResponseJson\Contract\Debug\ExceptionHandler'],
        ];

        foreach ($aliases as $key => $aliases) {
            foreach ((array)$aliases as $alias) {
                $this -> app -> alias($key, $alias);
            }
        }
    }


    /**
     * Register the exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {

        $this -> app -> singleton('responseJson.exception', function ($app) {
            $debug = $this -> app['config'] -> get('api.debug');
            return new ExceptionHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], $debug);
        });
    }
}
