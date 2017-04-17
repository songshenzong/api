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

    }


    /**
     * Register the class aliases.
     *
     * @return void
     */
    protected function registerClassAliases()
    {

        $this -> app -> alias('ResponseJson', 'Songshenzong\ResponseJson\Facade');


    }



}
