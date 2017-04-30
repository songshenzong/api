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
        if (!$this -> inPrefixes()) {
            return $next($request);
        }

        if ($this -> inExcludes()) {
            return $next($request);
        }

        if (!$this -> inDomains()) {
            return $next($request);
        }


        try {
            $response = $this -> sendRequestThroughRouter($request);
        } catch (Exception $exception) {
            if ($this -> isCompatibleWithDingo($exception)) {
                return $next($request);
            }

            $this -> exception -> report($exception);
            $response = $this -> exception -> handle($exception);
        }


        return $response;
    }

    /**
     * @param \Exception $exception
     *
     * @return bool
     */
    private function isCompatibleWithDingo(Exception $exception)
    {
        if (env('SONGSHENZONG_API_DINGO', false)) {
            if (method_exists($exception, 'getStatusCode') && $exception -> getStatusCode() == 404) {
                if ($this -> app['request'] -> segment(1) == env('API_PREFIX')) {
                    return true;
                }
            }
        }
    }


    /**
     * @return bool
     */
    private function inPrefixes()
    {
        $env = env('SONGSHENZONG_API_PREFIX', null);

        if ($env === true) {
            $env = 'true';
        }

        if ($env === false) {
            $env = 'false';
        }


        if (is_null($env)) {
            return true;
        }

        if ($env == '') {
            return true;
        }


        $array = explode(',', $env);

        if (in_array($this -> app['request'] -> segment(1), $array)) {
            return true;
        }
        return false;
    }


    /**
     * @return bool
     */
    private function inExcludes()
    {
        $env = env('SONGSHENZONG_API_EXCLUDE', null);

        if ($env === true) {
            $env = 'true';
        }

        if ($env === false) {
            $env = 'false';
        }

        if (is_null($env)) {
            return false;
        }

        if ($env == '') {
            return false;
        }

        $array = explode(',', $env);

        if (in_array($this -> app['request'] -> segment(1), $array)) {
            return true;
        }
        return false;
    }


    /**
     * @return bool
     */
    private function inDomains()
    {
        $env = env('SONGSHENZONG_API_DOMAIN', null);

        if ($env === true) {
            $env = 'true';
        }

        if ($env === false) {
            $env = 'false';
        }

        if (is_null($env)) {
            return true;
        }

        if ($env == '') {
            return true;
        }

        $array = explode(',', $env);

        if (in_array(request() -> getHttpHost(), $array)) {
            return true;
        }
        return false;
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

            return $response;
        });
    }
}
