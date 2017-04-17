<?php

namespace Dingo\Api\Http\Middleware;

use Closure;
use Exception;
use Dingo\Api\Routing\Router;
use Illuminate\Pipeline\Pipeline;
use Dingo\Api\Http\Request as HttpRequest;
use Illuminate\Contracts\Container\Container;
use Dingo\Api\Contract\Debug\ExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler as LaravelExceptionHandler;

class Request
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Exception handler instance.
     *
     * @var \Dingo\Api\Contract\Debug\ExceptionHandler
     */
    protected $exception;

    /**
     * Router instance.
     *
     * @var \Dingo\Api\Routing\Router
     */
    protected $router;


    /**
     * Array of middleware.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * Create a new request middleware instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Dingo\Api\Contract\Debug\ExceptionHandler   $exception
     * @param \Dingo\Api\Routing\Router                    $router
     *
     * @return void
     */
    public function __construct(Container $app, ExceptionHandler $exception, Router $router)
    {
        $this -> app       = $app;
        $this -> exception = $exception;
        $this -> router    = $router;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {
            $this -> app -> singleton(LaravelExceptionHandler::class, function ($app) {
                return $app[ExceptionHandler::class];
            });

            $request = $this -> app -> make(HttpRequest::class) -> createFromIlluminate($request);


            return $this -> sendRequestThroughRouter($request);

        } catch (Exception $exception) {


            $this -> exception -> report($exception);

            return $this -> exception -> handle($exception);
        }

        return $next($request);
    }


    public function dispatch(Request $request)
    {
        $this -> currentRoute = null;

        $this -> container -> instance(Request::class, $request);

        $this -> routesDispatched++;

        try {
            $response = $this -> adapter -> dispatch($request, $request -> version());

            if (property_exists($response, 'exception') && $response -> exception instanceof Exception) {
                throw $response -> exception;
            }
        } catch (Exception $exception) {
            if ($request instanceof InternalRequest) {
                throw $exception;
            }

            $this -> exception -> report($exception);

            $response = $this -> exception -> handle($exception);
        }

        return $this -> prepareResponse($response, $request, $request -> format());
    }


    /**
     * Send the request through the Dingo router.
     *
     * @param \Dingo\Api\Http\Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function sendRequestThroughRouter(HttpRequest $request)
    {
        $this -> app -> instance('request', $request);

        return (new Pipeline($this -> app)) -> send($request) -> through($this -> middleware) -> then(function ($request) {
            return $this -> router -> dispatch($request);
        });
    }


}
