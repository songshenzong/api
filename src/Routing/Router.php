<?php

namespace Dingo\Api\Routing;

use Closure;
use Exception;
use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Illuminate\Http\JsonResponse;
use Dingo\Api\Http\InternalRequest;
use Illuminate\Container\Container;
use Dingo\Api\Contract\Routing\Adapter;
use Dingo\Api\Contract\Debug\ExceptionHandler;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class Router
{
    /**
     * Routing adapter instance.
     *
     * @var \Dingo\Api\Contract\Routing\Adapter
     */
    protected $adapter;

    /**
     * Accept parser instance.
     *
     * @var \Dingo\Api\Http\Parser\Accept
     */
    protected $accept;

    /**
     * Exception handler instance.
     *
     * @var \Dingo\Api\Contract\Debug\ExceptionHandler
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
     * @var \Dingo\Api\Routing\Route
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
     * @param \Dingo\Api\Contract\Routing\Adapter        $adapter
     * @param \Dingo\Api\Http\Parser\Accept              $accept
     * @param \Dingo\Api\Contract\Debug\ExceptionHandler $exception
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
     * @param \Dingo\Api\Http\Request $request
     *
     * @throws \Exception
     *
     * @return \Dingo\Api\Http\Response
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
     * @param \Dingo\Api\Http\Request $request
     * @param string                  $format
     *
     * @return \Dingo\Api\Http\Response
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
