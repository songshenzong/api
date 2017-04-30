<?php

namespace Songshenzong\Api;

use Closure;
use Exception;
use Illuminate\Container\Container;
use Songshenzong\Api\Exception\Handler;
use Illuminate\Routing\Router;
use Illuminate\Pipeline\Pipeline;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

        try {
            return $this -> sendRequestThroughRouter($request);
        } catch (Exception $exception) {


            if ($this -> isCompatibleWithDingo($exception)) {
                return $next($request);

            }

            $this -> exception -> report($exception);
            return $this -> exception -> handle($exception);
        }


        return $next($request);
    }

    /**
     * @param \Exception $exception
     *
     * @return bool
     */
    private function isCompatibleWithDingo(Exception $exception)
    {
        if (method_exists($exception, 'getStatusCode') && $exception -> getStatusCode() == 404) {
            if (env('SONGSHENZONG_API_DINGO', false)) {
                if ($this -> app['request'] -> segment(1) == env('API_PREFIX')) {
                    return true;
                }
            }
        }
    }


    /**
     * Send the request through the Dingo router.
     *
     * @param \Dingo\Api\Http\Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function sendRequestThroughRouter($request)
    {
        return (new Pipeline($this -> app)) -> send($request) -> then(function ($request) {

            $response = $this -> router -> dispatch($request);

            if (property_exists($response, 'exception') && $response -> exception instanceof Exception) {
                if (method_exists($response, 'getStatusCode')) {
                    $response -> exception -> responseStatusCode = $response -> getStatusCode();
                }

                throw $response -> exception;
            }
        });
    }
}
