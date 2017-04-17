<?php

namespace Songshenzong\ResponseJson\Routing\Adapter;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Songshenzong\ResponseJson\Contract\Routing\Adapter;

class Laravel implements Adapter
{


    /**
     * Laravel router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;


    /**
     * Create a new laravel routing adapter instance.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function __construct(Router $router)
    {
        $this -> router = $router;
    }

    /**
     * Dispatch a request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $version
     *
     * @return mixed
     */
    public function dispatch(Request $request, $version)
    {
        $router = clone $this -> router;

        $response = $router -> dispatch($request);

        unset($router);

        return $response;
    }


}
