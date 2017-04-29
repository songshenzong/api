<?php

namespace Songshenzong\Api;

use Closure;
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
    public function handle($request, Closure $next)
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
            if (method_exists($exception, 'getStatusCode') && $exception -> getStatusCode() == 404) {
                if ($this -> app['request'] -> segment(1) == env('API_PREFIX')) {
                    return $next($request);
                }
            }

            $this -> exception -> report($exception);

            $response = $this -> exception -> handle($exception);

        }

        return $response;
    }
}
