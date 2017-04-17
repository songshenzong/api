<?php

namespace Dingo\Api\Provider;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use RuntimeException;
use Dingo\Api\Auth\Auth;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Dingo\Api\Exception\Handler as ExceptionHandler;
use Dingo\Api\Routing\Router;
use Dingo\Api\Contract\Routing\Adapter;

use Dingo\Api\Http\Parser\Accept as AcceptParser;

use ReflectionClass;
use Illuminate\Contracts\Http\Kernel;
use Dingo\Api\Http\Middleware\Request as RequestMiddleware;
use Dingo\Api\Routing\Adapter\Laravel;

class ServiceProvider extends IlluminateServiceProvider
{
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
        Request ::setAcceptParser($this -> app['Dingo\Api\Http\Parser\Accept']);
        $kernel = $this -> app -> make(Kernel::class);
        $kernel -> prependMiddleware(RequestMiddleware::class);

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
            'responseJson.router'         => 'Dingo\Api\Routing\Router',
            'responseJson.router.adapter' => 'Dingo\Api\Contract\Routing\Adapter',
            'responseJson.exception'      => ['Dingo\Api\Exception\Handler', 'Dingo\Api\Contract\Debug\ExceptionHandler'],
        ];

        foreach ($aliases as $key => $aliases) {
            foreach ((array)$aliases as $alias) {
                $this -> app -> alias($key, $alias);
            }
        }

        $this -> app -> singleton('responseJson.router', function ($app) {
            $router = new Router(
                $app[Adapter::class],
                $app[ExceptionHandler::class],
                $app
            );
            return $router;
        });


        $this -> app -> singleton(AcceptParser::class, function ($app) {
            return new AcceptParser(
                'x',
                '',
                'v1',
                'json'
            );
        });


        $this -> app -> singleton('responseJson.exception', function ($app) {
            return new ExceptionHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], $this -> config('errorFormat'), $this -> config('debug'));
        });


        $this -> app -> singleton('responseJson.router.adapter', function ($app) {
            return new Laravel($app['router']);
        });


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
}
