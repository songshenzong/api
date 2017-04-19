<?php

namespace Songshenzong\ResponseJson;

use Songshenzong\ResponseJson\Exception\Handler;

use Songshenzong\ResponseJson\Http\Request;
use Songshenzong\ResponseJson\Http\Response;

use Songshenzong\ResponseJson\Http\Parser\Accept as AcceptParser;

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
        $this -> app -> singleton(AcceptParser::class, function ($app) {
            return new AcceptParser(
                'x',
                '',
                'v1',
                'json'
            );
        });


        $this -> app -> singleton('ResponseJson', function ($app) {
            return new ResponseJson(
                $app,
                $app[Handler::class],
                $app[\Illuminate\Routing\Router::class]
            );
        });


        $this -> app -> singleton('responseJson.exception', function ($app) {
            return new Handler($app['Illuminate\Contracts\Debug\ExceptionHandler'], env('APP_DEBUG'));
        });


        $this -> app -> alias('ResponseJson', 'Songshenzong\ResponseJson\Facade');
        $this -> app -> alias('responseJson.exception', 'Songshenzong\ResponseJson\Exception\Handler');
    }


    /**
     * Retrieve and instantiate a config value if it exists and is a class.
     *
     * @param      $item
     * @param bool $instantiate
     *
     * @return array|mixed
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
