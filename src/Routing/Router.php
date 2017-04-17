<?php

namespace Songshenzong\ResponseJson\Routing;

use Closure;
use Exception;
use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Songshenzong\ResponseJson\Http\Request;
use Songshenzong\ResponseJson\Http\Response;
use Illuminate\Http\JsonResponse;
use Songshenzong\ResponseJson\Http\InternalRequest;
use Illuminate\Container\Container;
use Songshenzong\ResponseJson\Contract\Routing\Adapter;
use Songshenzong\ResponseJson\Contract\Debug\ExceptionHandler;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class Router
{
    /**
     * Routing adapter instance.
     *
     */
    protected $adapter;

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
     * Application container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;





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
     * Create a new router instance.
     *
     * @param \Illuminate\Container\Container            $container
     * @param string                                     $domain
     * @param string                                     $prefix
     *
     * @return void
     */
    public function __construct(Adapter $adapter, ExceptionHandler $exception, Container $container)
    {
        $this -> adapter   = $adapter;
        $this -> exception = $exception;
        $this -> container = $container;

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
