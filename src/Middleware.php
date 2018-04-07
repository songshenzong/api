<?php

namespace Songshenzong\Api;

use Closure;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Router;
use Songshenzong\Api\Exception\Handler;
use function config;

/**
 * Class Middleware
 *
 * @package Songshenzong\Api
 */
class Middleware
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Handler
     */
    protected $exception;
    /**
     * @var Router
     */
    protected $router;

    /**
     * Create a new request  instance.
     *
     * @param Container $app
     * @param Handler   $exception
     * @param Router    $router
     */
    public function __construct(Container $app, Handler $exception, Router $router)
    {
        $this->app       = $app;
        $this->exception = $exception;
        $this->router    = $router;
    }


    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @param Closure                  $next
     *
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {


        if (!$this->inPrefixes()) {
            return $next($request);
        }

        if ($this->inExcludes()) {
            return $next($request);
        }

        if (!$this->inDomains()) {
            return $next($request);
        }

        // CORS
        if (config('api.cors.cors', true)) {
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            } else {
                header('Access-Control-Allow-Origin: *');
            }
            if (config('api.cors.credentials', true)) {
                header('Access-Control-Allow-Credentials: true');
            }
            header("Access-Control-Allow-Headers: Origin, X-Requested-With, No-Cache, Authorization, X-Auth-Token, Content-Type, Accept");
            header("Access-Control-Allow-Methods: GET, POST, HEAD, PUT, DELETE, PATCH, OPTIONS, TRACE");
            $age = config('api.cors.max_age', 86400);
            header("Access-Control-Max-Age: $age");
        }


        try {
            $response = $this->sendRequestThroughRouter($request);
        } catch (Exception $exception) {
            if ($this->isCompatibleWithDingo($exception)) {
                return $next($request);
            }

            $this->exception->report($exception);
            $response = $this->exception->handle($exception);
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
        if (!config('api.dingo')) {
            return false;
        }


        if (method_exists($exception, 'getHttpStatusCode')) {
            if ($exception->getHttpStatusCode() !== 404) {
                return false;
            }
        }


        if (!method_exists($exception, 'getStatusCode')) {
            return false;
        }

        if ($exception->getStatusCode() !== 404) {
            return false;
        }

        if (app('request')->segment(1) === config('api.prefix')) {
            return true;
        }
        return false;
    }


    /**
     * @return bool
     */
    private function inPrefixes()
    {

        $array = $this->getEnvArray('prefix');


        if ($array === []) {
            return true;
        }


        return in_array(app('request')->segment(1), $array, true);
    }


    /**
     * @return bool
     */
    private function inExcludes()
    {

        $array = $this->getEnvArray('exclude');

        if ($array === []) {
            return false;
        }


        return in_array(app('request')->segment(1), $array, true);
    }


    /**
     * @param $name
     *
     * @return array
     */
    private function getEnvArray($name)
    {
        $env = config("api.$name", null);

        if ($env === true) {
            $env = 'true';
        }

        if ($env === false) {
            $env = 'false';
        }


        if ($env === null) {
            return [];
        }

        if ($env === '') {
            return [];
        }

        return explode(',', $env);
    }


    /**
     * @return bool
     */
    private function inDomains()
    {
        $array = $this->getEnvArray('domain');

        if ($array === []) {
            return true;
        }


        return in_array(app('request')->getHttpHost(), $array, true);
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
        return (new Pipeline($this->app))->send($request)->then(function ($request) {

            $response = $this->router->dispatch($request);

            if (property_exists($response, 'exception') && $response->exception instanceof Exception) {
                if (method_exists($response, 'getStatusCode')) {
                    $response->exception->responseStatusCode = $response->getStatusCode();
                }

                throw $response->exception;
            }

            return $response;
        });
    }
}
