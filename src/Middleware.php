<?php

namespace Songshenzong\Api;

use Exception;
use Illuminate\Container\Container;
use Songshenzong\Api\Exception\Handler;
use Illuminate\Routing\Router;

class Middleware
{
    protected $app;
    protected $exception;
    protected $router;

    /**
     * Create a new request  instance.
     *
     */
    public function __construct(Container $app, Handler $exception, Router $router)
    {
        $this -> app       = $app;
        $this -> exception = $exception;
        $this -> router    = $router;
    }


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request)
    {
        // return $next($request);

        try {
            $router = clone $this -> router;

            $response = $router -> dispatch($request);

            if (property_exists($response, 'exception') && $response -> exception instanceof Exception) {
                if (method_exists($response, 'getStatusCode')) {
                    $response -> exception -> responseStatusCode = $response -> getStatusCode();
                }

                throw $response -> exception;
            }
        } catch (Exception $exception) {
            // For dingo/api
            // if (isset($response) && $response -> getStatusCode() === 404) {
            //     return $next($request);
            // }
            $this -> exception -> report($exception);
            $response = $this -> exception -> handle($exception);
        }

        return $response;
    }
}
