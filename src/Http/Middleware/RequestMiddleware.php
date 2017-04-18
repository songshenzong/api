<?php

namespace Songshenzong\ResponseJson\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Pipeline\Pipeline;
use Songshenzong\ResponseJson\Http\Request as HttpRequest;
use Illuminate\Contracts\Container\Container;
use Songshenzong\ResponseJson\Contract\Debug\ExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler as LaravelExceptionHandler;


use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Songshenzong\ResponseJson\Http\Request;
use Songshenzong\ResponseJson\Http\Response;
use Illuminate\Http\JsonResponse;
use Songshenzong\ResponseJson\Contract\Routing\Adapter;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class RequestMiddleware
{

    /**
     * Routing adapter instance.
     *
     */
    protected $adapter;
    protected $app;


    /**
     * Accept parser instance.
     *
     */
    protected $accept;

    /**
     * Exception handler instance.
     *
     */
    protected $exception;



    /**
     * The current route being dispatched.
     *
     */
    protected $currentRoute;

    /**
     * The number of routes dispatched.
     *
     * @var int
     */
    protected $routesDispatched = 0;


    /**
     * Create a new request  instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(Container $app, ExceptionHandler $exception, Adapter $adapter)
    {
        $this -> app       = $app;
        $this -> exception = $exception;
        $this -> adapter   = $adapter;
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

            $this -> app -> instance('request', $request);

            return (new Pipeline($this -> app)) -> send($request) -> then(function ($request) {
                return $this ->  dispatch($request);
            });

        } catch (Exception $exception) {


            $this -> exception -> report($exception);

            return $this -> exception -> handle($exception);
        }

        return $next($request);
    }





    /**
     * Dispatch a request via the adapter.
     *
     *
     * @throws \Exception
     *
     */
    public function dispatch(Request $request)
    {
        $this -> currentRoute = null;

        $this -> app -> instance(Request::class, $request);

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
     * Prepare a response by transforming and formatting it correctly.
     *
     * @param mixed                   $response
     * @param string                  $format
     *
     */
    protected function prepareResponse($response, Request $request, $format)
    {

        if ($response instanceof IlluminateResponse) {
            $response = Response ::makeFromExisting($response);
        } elseif ($response instanceof JsonResponse) {
            $response = Response ::makeFromJson($response);
        }

        if ($response instanceof Response) {
            // If we try and get a formatter that does not exist we'll let the exception
            // handler deal with it. At worst we'll get a generic JSON response that
            // a consumer can hopefully deal with. Ideally they won't be using
            // an unsupported format.
            try {
                $response -> getFormatter($format) -> setResponse($response) -> setRequest($request);
            } catch (NotAcceptableHttpException $exception) {
                return $this -> exception -> handle($exception);
            }

            $response = $response -> morph($format);
        }


        return $response;
    }
}
