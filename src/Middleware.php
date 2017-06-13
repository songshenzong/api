<?php

namespace Songshenzong\Api;

use Closure;
use function dd;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Router;
use Songshenzong\Api\Exception\Handler;
use const false;

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
        if (!env('SONGSHENZONG_API_DINGO', false)) {
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

        if ($this->app['request']->segment(1) === env('API_PREFIX')) {
            return true;
        }
        return false;
    }


    /**
     * @return bool
     */
    private function inPrefixes()
    {

        $array = $this->getEnvArray('SONGSHENZONG_API_PREFIX');

        if ($array === []) {
            return true;
        }

        if (in_array($this->app['request']->segment(1), $array, true)) {
            return true;
        }

        return false;
    }


    /**
     * @return bool
     */
    private function inExcludes()
    {

        $array = $this->getEnvArray('SONGSHENZONG_API_EXCLUDE');

        if ($array === []) {
            return false;
        }

        if (in_array($this->app['request']->segment(1), $array, true)) {
            return true;
        }
        return false;
    }


    /**
     * @param $name
     *
     * @return array
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


        $array = $this->getEnvArray('SONGSHENZONG_API_DOMAIN');

        if ($array === []) {
            return true;
        }

        $array = explode(',', $array);

        if (in_array(request()->getHttpHost(), $array, true)) {
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
