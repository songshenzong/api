<?php

namespace Songshenzong\ResponseJson;

use Closure;
use Exception;
use RuntimeException;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;

use Songshenzong\ResponseJson\Exception\Handler;
use Songshenzong\ResponseJson\Exception\ResourceException;
use Songshenzong\ResponseJson\Http\Request as HttpRequest;
use Songshenzong\ResponseJson\Http\Response;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class ResponseJson
{

    /**---------------------------------------------------------
     *   Json Part
     *---------------------------------------------------------*/


    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var int
     */
    protected $httpStatusCode;

    /**
     * @var string
     */
    protected $content = [];

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $errors;


    protected $notException;


    /**---------------------------------------------------------
     *   Midleware Part
     *---------------------------------------------------------*/


    protected $app;
    protected $exception;
    protected $router;

    /**
     * Create a new request  instance.
     *
     */
    public function __construct(Container $app, Handler $exception, \Illuminate\Routing\Router $router)
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
        $this -> request = $request;
        // return $next($request);
        try {
            $this -> app -> singleton(ExceptionHandler::class, function ($app) {
                return $app[Handler::class];
            });

            $request = $this -> app -> make(HttpRequest::class) -> createFromIlluminate($request);

            // $this -> app -> instance('request', $request);


            return (new Pipeline($this -> app)) -> send($request) -> then(function ($request) {
                return $this -> dispatch($request);
            });
        } catch (Exception $exception) {
            $this -> exception -> report($exception);

            return $this -> exception -> handle($exception);
        }


        return $next($request);
    }


    /**
     * Dispatch a request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function adapterdispatch(\Illuminate\Http\Request $request)
    {
        $router = clone $this -> router;

        $response = $router -> dispatch($request);

        unset($router);

        return $response;
    }

    /**
     * Dispatch a request via the adapter.
     *
     *
     * @throws \Exception
     *
     */
    public function dispatch(HttpRequest $request)
    {
        // $this -> app -> instance(HttpRequest::class, $request);


        try {
            $response = $this -> adapterdispatch($request);

            if (property_exists($response, 'exception') && $response -> exception instanceof Exception) {
                throw $response -> exception;
            }
        } catch (Exception $exception) {
            $this -> exception -> report($exception);

            $response = $this -> exception -> handle($exception);
        }

        return $this -> prepareResponse($response, $request, $request -> format());
    }

    /**
     * Prepare a response by transforming and formatting it correctly.
     *
     * @param mixed  $response
     * @param string $format
     *
     */
    protected function prepareResponse($response, HttpRequest $request, $format)
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


    /**---------------------------------------------------------
     *   Midleware Part End
     *---------------------------------------------------------*/


    /**
     * @param mixed $notException
     *
     * @return $this
     */
    public function setNotException($notException = true)
    {
        $this -> notException = $notException;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotException()
    {
        return $this -> notException;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this -> message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this -> message;
    }


    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this -> data = $data;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getData()
    {
        return $this -> data;
    }


    /**
     * @param mixed $errors
     *
     * @return $this
     */
    public function setErrors($errors)
    {
        $this -> errors = $errors;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this -> errors;
    }

    /**
     * @param int $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this -> statusCode = $statusCode;
        return $this;
    }


    /**
     * @param int $httpStatusCode
     *
     * @return $this
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this -> httpStatusCode = $httpStatusCode;
        return $this;
    }


    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this -> httpStatusCode;
    }


    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this -> statusCode;
    }


    /**
     * Public Success Method.
     *
     * @param      $statusCode
     * @param      $message
     * @param null $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($statusCode, $message, $data = null)
    {
        $this -> setStatusCode($statusCode);


        $this -> setMessage($message);
        $this -> setData($data);

        $this -> content = [
            'message'     => $this -> message,
            'status_code' => $this -> statusCode,
        ];


        if (is_null($this -> getHttpStatusCode())) {
            $statusCode = $this -> getStatusCode();
        } else {
            $statusCode                          = $this -> getHttpStatusCode();
            $this -> content['http_status_code'] = $statusCode;
        }


        if (!is_null($this -> getErrors())) {
            $this -> content['errors'] = $this -> getErrors();
        }


        if (!is_null($this -> getData()) && $this -> getData() != $this -> getErrors()) {
            $this -> setData($data);
            $this -> content['data'] = $this -> getData();
        }


        return \Response ::json($this -> content, $statusCode);
    }


    /**
     * Success - OK
     *
     * Standard response for successful HTTP requests. The actual response will depend on the request method used. In a
     * GET request, the response will contain an entity corresponding to the requested resource. In a POST request, the
     * response will contain an entity describing or containing the result of the action.
     *
     * @param null $data
     *
     * @return mixed
     */
    public function ok($data = null)
    {
        return $this -> success(200, 'OK', $data);
    }


    /**
     * @param null $data
     *
     * @return mixed
     */
    public function item($data = null)
    {
        return $this -> ok($data);
    }

    /**
     * @param null $data
     *
     * @return mixed
     */
    public function collection($data = null)
    {
        return $this -> ok($data);
    }

    /**
     * @param null $data
     *
     * @return mixed
     */
    public function paginate($data = null)
    {
        return $this -> ok($data);
    }

    /**
     * Success - Created
     *
     * The request has been fulfilled, resulting in the creation of a new resource.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function created($message = 'Created', $data = null)
    {
        return $this -> success(201, $message, $data);
    }

    /**
     * Success - Accepted
     *
     * The request has been accepted for processing, but the processing has not been completed. The request might or
     * might not be eventually acted upon, and may be disallowed when processing occurs.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function accepted($message = 'Accepted', $data = null)
    {
        return $this -> success(202, $message, $data);
    }

    /**
     * Success - Non-Authoritative Information
     *
     * The server is a transforming proxy (e.g. a Web accelerator) that received a 200 OK from its origin, but is
     * returning a modified version of the origin's response.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function nonAuthoritativeInformation($message = 'Non-Authoritative Information', $data = null)
    {
        return $this -> success(203, $message, $data);
    }


    /**
     * Success - No Content
     *
     * The server successfully processed the request and is not returning any content.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function noContent($message = 'No Content', $data = null)
    {
        return $this -> success(204, $message, $data);
    }


    /**
     * Success - Reset Content
     *
     * The server successfully processed the request, but is not returning any content. Unlike a 204 response, this
     * response requires that the requester reset the document view.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function resetContent($message = 'Reset Content', $data = null)
    {
        return $this -> success(205, $message, $data);
    }


    /**
     * Public Errors Exception Method.
     */
    public function errors($statusCode, $message, $errors = null)
    {
        $this -> setStatusCode($statusCode);


        $this -> setMessage($message);


        $this -> setErrors($errors);


        if ($this -> getNotException()) {
            return $this -> success($statusCode, $message, $errors);
        }


        if ($this -> getHttpStatusCode()) {
            $httpStatusCode = $this -> getHttpStatusCode();
        } else {
            $httpStatusCode = $this -> getStatusCode();
        }


        throw new ResourceException($httpStatusCode, $this -> getStatusCode(), $this -> getMessage(), $this -> getErrors());
    }


    /**
     * Client errors - Bad Request
     *
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request
     * syntax, too large size, invalid request message framing, or deceptive request routing).
     *
     * @param string $message
     */
    public function badRequest($message = 'Bad Request', $errors = null)
    {
        return $this -> errors(400, $message, $errors);
    }


    /**
     * Client errors - Unauthorized
     *
     * Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet
     * been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to
     * the requested resource. See Basic access authentication and Digest access authentication.[32] 401 semantically
     * means "unauthenticated",[33] i.e. the user does not have the necessary credentials.
     *
     * Note: Some sites issue HTTP 401 when an IP address is banned from the website (usually the website domain) and
     * that specific address is refused permission to access a website.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function unauthorized($message = 'Unauthorized', $errors = null)
    {
        return $this -> errors(401, $message, $errors);
    }


    /**
     * Client errors - Forbidden
     *
     * The request was valid, but the server is refusing action. The user might not have the necessary permissions for
     * a resource.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function forbidden($message = 'Forbidden', $errors = null)
    {
        return $this -> errors(403, $message, $errors);
    }


    /**
     * Client errors - Not Found
     *
     * The requested resource could not be found but may be available in the future. Subsequent requests by the client
     * are permissible.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function notFound($message = 'Not Found', $errors = null)
    {
        return $this -> errors(404, $message, $errors);
    }


    /**
     * Client errors - Method Not Allowed
     *
     * A request method is not supported for the requested resource; for example, a GET request on a form that requires
     * data to be presented via POST, or a PUT request on a read-only resource.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function methodNotAllowed($message = 'Method Not Allowed', $errors = null)
    {
        return $this -> errors(405, $message, $errors);
    }


    /**
     * Client errors - Not Acceptable
     *
     * The requested resource is capable of generating only content not acceptable according to the Accept headers sent
     * in the request.[36] See Content negotiation.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function notAcceptable($message = 'Not Acceptable', $errors = null)
    {
        return $this -> errors(406, $message, $errors);
    }


    /**
     * Client errors - Conflict
     *
     * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict
     * between multiple simultaneous updates.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function conflict($message = 'Conflict', $errors = null)
    {
        return $this -> errors(409, $message, $errors);
    }


    /**
     * Client errors - Gone
     *
     * Indicates that the resource requested is no longer available and will not be available again. This should be
     * used when a resource has been intentionally removed and the resource should be purged. Upon receiving a 410
     * status code, the client should not request the resource in the future. Clients such as search engines should
     * remove the resource from their indices.[40] Most use cases do not require clients and search engines to purge
     * the resource, and a "404 Not Found" may be used instead.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function gone($message = 'Gone', $errors = null)
    {
        return $this -> errors(410, $message, $errors);
    }


    /**
     * Client errors - Length Required
     *
     * The request did not specify the length of its content, which is required by the requested resource.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function lengthRequired($message = 'Length Required', $errors = null)
    {
        return $this -> errors(411, $message, $errors);
    }


    /**
     * Client errors - Precondition Failed
     *
     * The server does not meet one of the preconditions that the requester put on the request.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function preconditionFailed($message = 'Precondition Failed', $errors = null)
    {
        return $this -> errors(412, $message, $errors);
    }


    /**
     * Client errors - Unsupported Media Type
     *
     * The request entity has a media type which the server or resource does not support. For example, the client
     * uploads an image as image/svg+xml, but the server requires that images use a different format.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function unsupportedMediaType($message = 'Unsupported Media Type', $errors = null)
    {
        return $this -> errors(413, $message, $errors);
    }


    /**
     * Client errors - Unprocessable Entity
     *
     * The request was well-formed but was unable to be followed due to semantic errors.[15].
     *
     * @param string $message
     *
     * @return mixed
     */
    public function unprocessableEntity($message = 'Unprocessable Entity', $errors = null)
    {
        return $this -> errors(422, $message, $errors);
    }


    /**
     * Client errors - Precondition Required
     *
     * The origin server requires the request to be conditional. Intended to prevent "the 'lost update' problem, where
     * a client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has
     * modified the state on the server, leading to a conflict.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function preconditionRequired($message = 'Precondition Required', $errors = null)
    {
        return $this -> errors(428, $message, $errors);
    }


    /**
     * Client errors - Too Many Requests
     *
     * The user has sent too many requests in a given amount of time. Intended for use with rate-limiting schemes.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function tooManyRequests($message = 'Too Many Requests', $errors = null)
    {
        return $this -> errors(429, $message, $errors);
    }


    /**
     * Server error - Internal Server Error
     *
     * A generic error message, given when an unexpected condition was encountered and no more specific message is
     * suitable.
     *
     * @param string $message
     */
    public function internalServerError($message = 'Internal Server Error', $errors = null)
    {
        return $this -> errors(500, $message, $errors);
    }


    /**
     * Server error - Not Implemented
     *
     * The server either does not recognize the request method, or it lacks the ability to fulfill the request. Usually
     * this implies future availability (e.g., a new feature of a web-service API).
     *
     * @param string $message
     */
    public function notImplemented($message = 'Not Implemented', $errors = null)
    {
        return $this -> errors(501, $message, $errors);
    }


    /**
     * Server error - Bad Gateway
     *
     * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
     *
     * @param string $message
     */
    public function badGateway($message = 'Bad Gateway', $errors = null)
    {
        return $this -> errors(502, $message, $errors);
    }


    /**
     * Server error - Service Unavailable
     *
     * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a
     * temporary state.
     *
     * @param string $message
     */
    public function serviceUnavailable($message = 'Service Unavailable', $errors = null)
    {
        return $this -> errors(503, $message, $errors);
    }


    /**
     * Server error - Gateway Time-out
     *
     * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
     *
     * @param string $message
     */
    public function gatewayTimeOut($message = 'Gateway Time-out', $errors = null)
    {
        return $this -> errors(504, $message, $errors);
    }


    /**
     * Server error - HTTP Version Not Supported
     *
     * The server does not support the HTTP protocol version used in the request.
     *
     * @param string $message
     */
    public function httpVersionNotSupported($message = 'HTTP Version Not Supported', $errors = null)
    {
        return $this -> errors(505, $message, $errors);
    }


    /**
     * Server error - Variant Also Negotiates
     *
     * Transparent content negotiation for the request results in a circular reference.
     *
     * @param string $message
     */
    public function variantAlsoNegotiates($message = 'Variant Also Negotiates', $errors = null)
    {
        return $this -> errors(506, $message, $errors);
    }


    /**
     * Server error - Insufficient Storage
     *
     * The server is unable to store the representation needed to complete the request.
     *
     * @param string $message
     */
    public function insufficientStorage($message = 'Insufficient Storage', $errors = null)
    {
        return $this -> errors(507, $message, $errors);
    }


    /**
     * Server error - Loop Detected
     *
     * The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
     *
     * @param string $message
     */
    public function loopDetected($message = 'Loop Detected', $errors = null)
    {
        return $this -> errors(508, $message, $errors);
    }


    /**
     * Server error - Not Extended
     *
     * Further extensions to the request are required for the server to fulfill it.
     *
     * @param string $message
     */
    public function notExtended($message = 'Not Extended', $errors = null)
    {
        return $this -> errors(510, $message, $errors);
    }


    /**
     * Server error - Network Authentication Required
     *
     * The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to
     * control access to the network (e.g., "captive portals" used to require agreement to Terms of Service before
     * granting full Internet access via a Wi-Fi hotspot).
     *
     * @param string $message
     */
    public function networkAuthenticationRequired($message = 'Network Authentication Required', $errors = null)
    {
        return $this -> errors(511, $message, $errors);
    }
}
