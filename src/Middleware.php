<?php

namespace Songshenzong\Api;

use Closure;
use Exception;
use Illuminate\Container\Container;
use Songshenzong\Api\Exception\Handler;
use Illuminate\Routing\Router;
use Illuminate\Pipeline\Pipeline;

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


        $array = $this -> getEnvArray('SONGSHENZONG_API_PREFIX');

        if ($array == null) {
            return true;
        }

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

        $array = $this -> getEnvArray('SONGSHENZONG_API_EXCLUDE');

        if ($array == null) {
            return false;
        }

        if (in_array($this -> app['request'] -> segment(1), $array)) {
            return true;
        }
        return false;
    }

    /**
     * @param $name
     *
     * @return array|null
     */
    private function getEnvArray($name)
    {
        $env = env($name, null);

        if ($env === true) {
            $env = 'true';
        }

        if ($env === false) {
            $env = 'false';
        }


        if ($env == null) {
            return null;
        }

        if ($env == '') {
            return null;
        }

        return explode(',', $env);

    }


    /**
     * @return bool
     */
    private function inDomains()
    {


        $array = $this -> getEnvArray('SONGSHENZONG_API_DOMAIN');

        if ($array == null) {
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
