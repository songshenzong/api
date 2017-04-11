<?php

namespace Songshenzong\ResponseJson;

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
            return new ResponseJson($app);
        });

        $this -> app -> alias('ResponseJson', 'Songshenzong\ResponseJson\Facade');
    }

}
