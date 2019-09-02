<?php

namespace Songshenzong\Api\Exception;

use Exception;
use ReflectionFunction;
use Illuminate\Http\Response;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class Handler
 *
 * @package Songshenzong\Api\Exception
 */
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
     * @param ExceptionHandler $parentHandler
     * @param                  $debug
     */
    public function __construct(ExceptionHandler $parentHandler, $debug)
    {
        $this->parentHandler = $parentHandler;
        $this->debug         = $debug;
    }

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception) : void
    {
        $this->parentHandler->report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param            $request
     * @param Exception  $exception
     *
     * @return mixed
     * @throws Exception
     *
     */
    public function render($request, Exception $exception)
    {
        return $this->handle($exception);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param Exception                                         $exception
     */
    public function renderForConsole($output, Exception $exception) : void
    {
        $this->parentHandler->renderForConsole($output, $exception);
    }

    /**
     * Register a new exception handler.
     *
     * @param callable $callback
     *
     * @return void
     * @throws \ReflectionException
     */
    public function register(callable $callback) : void
    {
        $hint = $this->handlerHint($callback);

        $this->handlers[$hint] = $callback;
    }

    /**
     * Handle an exception if it has an existing handler.
     *
     * @param Exception $exception
     *
     * @return Response
     * @throws Exception
     */
    public function handle(Exception $exception) : Response
    {
        foreach ($this->handlers as $hint => $handler) {
            if (!$exception instanceof $hint) {
                continue;
            }

            if ($response = $handler($exception)) {
                if (!$response instanceof BaseResponse) {
                    $response = new Response($response, $this->getExceptionStatusCode($exception));
                }

                return $response;
            }
        }

        return $this->genericResponse($exception);
    }

    /**
     * Handle a generic error response if there is no handler available.
     *
     * @param Exception $exception
     *
     * @return \Illuminate\Http\Response
     * @throws Exception
     *
     */
    protected function genericResponse(Exception $exception) : Response
    {
        $replacements = $this->prepareReplacements($exception);

        return new Response($replacements, $this->getHttpStatusCode($exception), $this->getHeaders($exception));
    }

    /**
     * Get the status code from the exception.
     *
     * @param Exception $exception
     *
     * @return int
     */
    protected function getStatusCode(Exception $exception) : int
    {
        if ($exception instanceof ApiException) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof HttpExceptionInterface && method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    /**
     * Get the Http status code from the exception.
     *
     * @param Exception $exception
     *
     * @return int
     */
    protected function getHttpStatusCode(Exception $exception) : int
    {
        if ($exception instanceof ApiException) {
            return $exception->apiMessage->getHttpStatusCode();
        }

        if ($exception instanceof HttpExceptionInterface && method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    /**
     * Get the headers from the exception.
     *
     * @param Exception $exception
     *
     * @return array
     */
    protected function getHeaders(Exception $exception) : array
    {
        return $exception instanceof ApiException ? $exception->getHeaders() : [];
    }

    /**
     * Prepare the replacements array by gathering the keys and values.
     *
     * @param Exception $exception
     *
     * @return array
     */
    protected function prepareReplacements(Exception $exception) : array
    {
        $statusCode = $this->getStatusCode($exception);

        if (!$message = $exception->getMessage()) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        $replacements['message'] = $message;

        if ($code = $exception->getCode()) {
            $replacements['code'] = $code;
        }

        $replacements['status_code'] = $statusCode;

        if ($exception instanceof ApiException && $exception->apiMessage->getErrors()) {
            $replacements['errors'] = $exception->apiMessage->getErrors();
        }

        if ($exception instanceof ApiException && null !== $exception->apiMessage->getHypermedia()) {
            $replacements += $exception->apiMessage->getHypermedia();
        }

        if ($this->runningInDebugMode()) {
            $replacements['debug'] = [
                'line'  => $exception->getLine(),
                'file'  => $exception->getFile(),
                'code'  => $exception->getCode(),
                'class' => \get_class($exception),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        return array_merge($replacements, $this->replacements);
    }

    /**
     * Set user defined replacements.
     *
     * @param array $replacements
     *
     * @return void
     */
    public function setReplacements(array $replacements) : void
    {
        $this->replacements = $replacements;
    }

    /**
     * Get the exception status code.
     *
     * @param Exception $exception
     * @param int       $defaultStatusCode
     *
     * @return int
     */
    protected function getExceptionStatusCode(Exception $exception, $defaultStatusCode = null) : int
    {
        if (null === $defaultStatusCode) {
            $defaultStatusCode = 500;
        }

        return ($exception instanceof HttpExceptionInterface) ? $exception->getStatusCode() : $defaultStatusCode;
    }

    /**
     * Determines if we are running in debug mode.
     *
     * @return bool
     */
    protected function runningInDebugMode() : bool
    {
        return (bool)$this->debug;
    }

    /**
     * Get the hint for an exception handler.
     *
     * @param callable $callback
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function handlerHint(callable $callback) : string
    {
        $reflection = new ReflectionFunction($callback);

        $exception = $reflection->getParameters()[0];

        return $exception->getClass()->getName();
    }

    /**
     * Get the exception handlers.
     *
     * @return array
     */
    public function getHandlers() : array
    {
        return $this->handlers;
    }

    /**
     * Set the debug mode.
     *
     * @param bool $debug
     *
     * @return void
     */
    public function setDebug($debug) : void
    {
        $this->debug = $debug;
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param Exception $e
     *
     * @return bool
     */
    public function shouldReport(Exception $e) : bool
    {
        return true;
    }
}
