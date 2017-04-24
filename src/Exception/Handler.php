<?php

namespace Songshenzong\ResponseJson\Exception;

use Exception;
use ReflectionFunction;

use Illuminate\Http\Response;
use Illuminate\Contracts\Debug\ExceptionHandler;


use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Songshenzong\ResponseJson\Exception\HttpException;

class Handler implements ExceptionHandler
{
    /**
     * Array of exception handlers.
     *
     * @var array
     */
    protected $handlers = [];


    /**
     * Indicates if we are in debug mode.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * User defined replacements to merge with defaults.
     *
     * @var array
     */
    protected $replacements = [];

    /**
     * The parent Illuminate exception handler instance.
     *
     */
    protected $parentHandler;

    /**
     * Create a new exception handler instance.
     *
     *
     */
    public function __construct(ExceptionHandler $parentHandler, $debug)
    {
        $this -> parentHandler = $parentHandler;
        $this -> debug         = $debug;
    }

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        $this -> parentHandler -> report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Songshenzong\ResponseJson\Http\Request $request
     * @param \Exception                              $exception
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function render($request, Exception $exception)
    {
        return $this -> handle($exception);
    }

    /**
     * Render an exception to the console.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception                                        $exception
     *
     * @return mixed
     */
    public function renderForConsole($output, Exception $exception)
    {
        return $this -> parentHandler -> renderForConsole($output, $exception);
    }

    /**
     * Register a new exception handler.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function register(callable $callback)
    {
        $hint = $this -> handlerHint($callback);

        $this -> handlers[$hint] = $callback;
    }

    /**
     * Handle an exception if it has an existing handler.
     *
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Exception $exception)
    {
        foreach ($this -> handlers as $hint => $handler) {
            if (!$exception instanceof $hint) {
                continue;
            }

            if ($response = $handler($exception)) {
                if (!$response instanceof BaseResponse) {
                    $response = new Response($response, $this -> getExceptionStatusCode($exception));
                }

                return $response;
            }
        }

        return $this -> genericResponse($exception);
    }

    /**
     * Handle a generic error response if there is no handler available.
     *
     * @param \Exception $exception
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function genericResponse(Exception $exception)
    {
        $replacements = $this -> prepareReplacements($exception);

        return new Response($replacements, $this -> getHttpStatusCode($exception), $this -> getHeaders($exception));
    }

    /**
     * Get the status code from the exception.
     *
     * @param \Exception $exception
     *
     * @return int
     */
    protected function getStatusCode(Exception $exception)
    {
        return $exception instanceof Exception ? $exception -> getStatusCode() : 500;
    }

    /**
     * Get the Http status code from the exception.
     *
     * @param \Exception $exception
     *
     * @return int
     */
    protected function getHttpStatusCode(Exception $exception)
    {
        return $exception instanceof HttpException ? $exception -> getHttpStatusCode() : $exception -> getStatusCode();
    }


    /**
     * Get the headers from the exception.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    protected function getHeaders(Exception $exception)
    {
        return $exception instanceof HttpException ? $exception -> getHeaders() : [];
    }

    /**
     * Prepare the replacements array by gathering the keys and values.
     *
     * @param \Exception $exception
     *
     * @return array
     */
    protected function prepareReplacements(Exception $exception)
    {
        $statusCode     = $this -> getStatusCode($exception);
        $httpStatusCode = $this -> getHttpStatusCode($exception);

        if (!$message = $exception -> getMessage()) {
            $message = sprintf('%d %s', $statusCode, Response ::$statusTexts[$statusCode]);
        }

        $replacements = [
            'message'     => $message,
            'status_code' => $statusCode,
        ];

        if ($statusCode != $httpStatusCode) {
            $replacements['http_status_code'] = $httpStatusCode;
        }


        if ($exception instanceof HttpException && $exception -> hasErrors()) {
            $replacements['errors'] = $exception -> getErrors();
        }


        if ($code = $exception -> getCode()) {
            $replacements['code'] = $code;
        }

        if ($this -> runningInDebugMode()) {
            $replacements['debug'] = [
                'line'  => $exception -> getLine(),
                'file'  => $exception -> getFile(),
                'class' => get_class($exception),
                'trace' => explode("\n", $exception -> getTraceAsString()),
            ];
        }

        $response = array_merge($replacements, $this -> replacements);

        return $response;
    }

    /**
     * Set user defined replacements.
     *
     * @param array $replacements
     *
     * @return void
     */
    public function setReplacements(array $replacements)
    {
        $this -> replacements = $replacements;
    }


    /**
     * Get the exception status code.
     *
     * @param \Exception $exception
     * @param int        $defaultStatusCode
     *
     * @return int
     */
    protected function getExceptionStatusCode(Exception $exception, $defaultStatusCode = 500)
    {
        return ($exception instanceof HttpException) ? $exception -> getStatusCode() : $defaultStatusCode;
    }

    /**
     * Determines if we are running in debug mode.
     *
     * @return bool
     */
    protected function runningInDebugMode()
    {
        return $this -> debug;
    }

    /**
     * Get the hint for an exception handler.
     *
     * @param callable $callback
     *
     * @return string
     */
    protected function handlerHint(callable $callback)
    {
        $reflection = new ReflectionFunction($callback);

        $exception = $reflection -> getParameters()[0];

        return $exception -> getClass() -> getName();
    }

    /**
     * Get the exception handlers.
     *
     * @return array
     */
    public function getHandlers()
    {
        return $this -> handlers;
    }


    /**
     * Set the debug mode.
     *
     * @param bool $debug
     *
     * @return void
     */
    public function setDebug($debug)
    {
        $this -> debug = $debug;
    }
}
