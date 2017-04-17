<?php

namespace Songshenzong\ResponseJson;

use Songshenzong\ResponseJson\Exception\Handler as ExceptionHandler;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this -> app -> singleton('ResponseJson', function ($app) {
            return new ResponseJson(
                $app[ExceptionHandler::class],
                $app
            );
        });


        $this -> registerClassAliases();
        $this -> registerExceptionHandler();
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
            $errorFormat = $this -> app['config'] -> get('api.errorFormat');
            $debug       = $this -> app['config'] -> get('api.debug');
            return new ExceptionHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], $errorFormat, $debug);
        });
    }
}
