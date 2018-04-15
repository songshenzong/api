<?php

namespace Songshenzong\Api;

use Illuminate\Http\JsonResponse;
use Songshenzong\Api\Exception\SongshenzongException;
use const true;

/**
 * Class Api
 *
 * @package Songshenzong\Api
 */
class Api
{


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
     * @var
     */
    protected $code;

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $errors;


    /**
     * Status codes translation table.
     *
     * The list of codes is complete according to the
     * {@link http://www.iana.org/assignments/http-status-codes/ Hypertext Transfer Protocol (HTTP) Status Code
     * Registry}
     * (last updated 2016-03-01).
     *
     * Unless otherwise noted, the status code is defined in RFC2616.
     *
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',                      // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    ];


    /**
     * Public Errors Exception Method.
     *
     * @param      $statusCode
     * @param      $message
     * @param null $errors
     *
     * @throws SongshenzongException
     */
    public function errors($statusCode, $message, $errors = null)
    {

        if (null === $this->getStatusCode()) {
            $this->setStatusCode($statusCode);
        }

        if (null === $this->getMessage()) {
            $this->setMessage($message);
        }

        if (null === $this->getErrors()) {
            $this->setErrors($errors);
        }

        throw new SongshenzongException(
            $this->getHttpStatusCode() ?: $this->getStatusCode(),
            $this->getStatusCode(),
            $this->getMessage(),
            $this->getErrors(),
            $this->getCode()
        );
    }


    /**
     * @param mixed $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    protected function getMessage()
    {
        return $this->message;
    }


    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }


    /**
     * @return mixed
     */
    protected function getData()
    {
        return $this->data;
    }


    /**
     * @param mixed $errors
     *
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getErrors()
    {
        return $this->errors;
    }


    /**
     * @param int $httpStatusCode
     *
     * @return mixed|Api
     * @throws SongshenzongException
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        if (!array_key_exists($httpStatusCode, self::$statusTexts)) {
            return $this->internalServerError('Invalid status code in ' . __METHOD__, self::$statusTexts);
        }
        $this->httpStatusCode = $httpStatusCode;
        return $this;
    }


    /**
     * @return int
     */
    protected function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }


    /**
     * @param $statusCode
     *
     * @return mixed|Api
     * @throws SongshenzongException
     */
    public function setStatusCode($statusCode)
    {
        if (!array_key_exists($statusCode, self::$statusTexts)) {
            return $this->internalServerError('Invalid status code in ' . __METHOD__, self::$statusTexts);
        }

        $this->statusCode = $statusCode;

        return $this;
    }


    /**
     * @return int
     */
    protected function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * Public Success Method.
     *
     * @param      $statusCode
     * @param      $message
     * @param null $data
     *
     * @return JsonResponse
     * @throws SongshenzongException
     */
    public function success($statusCode, $message, $data = null)
    {


        if (null === $this->getStatusCode()) {
            $this->setStatusCode($statusCode);
        }

        if (null === $this->getMessage()) {
            $this->setMessage($message);
        }

        if (null === $this->getData()) {
            $this->setData($data);
        }


        $content['message'] = $this->getMessage();

        if (null !== $this->getCode()) {
            $content['code'] = $this->getCode();
        }

        $content['status_code'] = $this->getStatusCode();


        if (null !== $this->getData() && $this->getData() !== $this->getErrors()) {
            $content['data'] = $this->getData();
        }

        if (null !== $this->getErrors()) {
            $content['errors'] = $this->getErrors();
        }

        return new JsonResponse($content, $this->getHttpStatusCode() ?: $this->getStatusCode());
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
     * @throws SongshenzongException
     */
    public function ok($data = null)
    {
        return $this->success(200, 'OK', $data);
    }

    /**
     * @param null $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function item($data = null)
    {
        return $this->ok($data);
    }

    /**
     * @param null $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function collection($data = null)
    {
        return $this->ok($data);
    }

    /**
     * @param null $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function paginate($data = null)
    {
        return $this->ok($data);
    }

    /**
     * Success - Created
     *
     * The request has been fulfilled, resulting in the creation of a new resource.
     *
     * @param string $message
     * @param null   $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function created($message = 'Created', $data = null)
    {
        return $this->success(201, $message, $data);
    }

    /**
     * Success - Accepted
     *
     * The request has been accepted for processing, but the processing has not been completed. The request might or
     * might not be eventually acted upon, and may be disallowed when processing occurs.
     *
     * @param string $message
     * @param null   $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function accepted($message = 'Accepted', $data = null)
    {
        return $this->success(202, $message, $data);
    }

    /**
     * Success - Non-Authoritative Information
     *
     * The server is a transforming proxy (e.g. a Web accelerator) that received a 200 OK from its origin, but is
     * returning a modified version of the origin's response.
     *
     * @param string $message
     * @param null   $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function nonAuthoritativeInformation($message = 'Non-Authoritative Information', $data = null)
    {
        return $this->success(203, $message, $data);
    }


    /**
     * Success - No Content
     *
     * The server successfully processed the request and is not returning any content.
     *
     * @param string $message
     * @param null   $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function noContent($message = 'No Content', $data = null)
    {
        return $this->success(204, $message, $data);
    }


    /**
     * Success - Reset Content
     *
     * The server successfully processed the request, but is not returning any content. Unlike a 204 response, this
     * response requires that the requester reset the document view.
     *
     * @param string $message
     * @param null   $data
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function resetContent($message = 'Reset Content', $data = null)
    {
        return $this->success(205, $message, $data);
    }


    /**
     * Client errors - Bad Request
     *
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request
     * syntax, too large size, invalid request message framing, or deceptive request routing).
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function badRequest($message = 'Bad Request', $errors = null)
    {
        return $this->errors(400, $message, $errors);
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
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function unauthorized($message = 'Unauthorized', $errors = null)
    {
        return $this->errors(401, $message, $errors);
    }


    /**
     * Client errors - Forbidden
     *
     * The request was valid, but the server is refusing action. The user might not have the necessary permissions for
     * a resource.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function forbidden($message = 'Forbidden', $errors = null)
    {
        return $this->errors(403, $message, $errors);
    }


    /**
     * Client errors - Not Found
     *
     * The requested resource could not be found but may be available in the future. Subsequent requests by the client
     * are permissible.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function notFound($message = 'Not Found', $errors = null)
    {
        return $this->errors(404, $message, $errors);
    }


    /**
     * Client errors - Method Not Allowed
     *
     * A request method is not supported for the requested resource; for example, a GET request on a form that requires
     * data to be presented via POST, or a PUT request on a read-only resource.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function methodNotAllowed($message = 'Method Not Allowed', $errors = null)
    {
        return $this->errors(405, $message, $errors);
    }


    /**
     * Client errors - Not Acceptable
     *
     * The requested resource is capable of generating only content not acceptable according to the Accept headers sent
     * in the request.[36] See Content negotiation.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function notAcceptable($message = 'Not Acceptable', $errors = null)
    {
        return $this->errors(406, $message, $errors);
    }


    /**
     * Client errors - Conflict
     *
     * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict
     * between multiple simultaneous updates.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function conflict($message = 'Conflict', $errors = null)
    {
        return $this->errors(409, $message, $errors);
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
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function gone($message = 'Gone', $errors = null)
    {
        return $this->errors(410, $message, $errors);
    }


    /**
     * Client errors - Length Required
     *
     * The request did not specify the length of its content, which is required by the requested resource.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function lengthRequired($message = 'Length Required', $errors = null)
    {
        return $this->errors(411, $message, $errors);
    }


    /**
     * Client errors - Precondition Failed
     *
     * The server does not meet one of the preconditions that the requester put on the request.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function preconditionFailed($message = 'Precondition Failed', $errors = null)
    {
        return $this->errors(412, $message, $errors);
    }


    /**
     * Client errors - Unsupported Media Type
     *
     * The request entity has a media type which the server or resource does not support. For example, the client
     * uploads an image as image/svg+xml, but the server requires that images use a different format.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function unsupportedMediaType($message = 'Unsupported Media Type', $errors = null)
    {
        return $this->errors(413, $message, $errors);
    }


    /**
     * Client errors - Unprocessable Entity
     *
     * The request was well-formed but was unable to be followed due to semantic errors.[15].
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function unprocessableEntity($message = 'Unprocessable Entity', $errors = null)
    {
        return $this->errors(422, $message, $errors);
    }


    /**
     * Client errors - Precondition Required
     *
     * The origin server requires the request to be conditional. Intended to prevent "the 'lost update' problem, where
     * a client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has
     * modified the state on the server, leading to a conflict.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function preconditionRequired($message = 'Precondition Required', $errors = null)
    {
        return $this->errors(428, $message, $errors);
    }


    /**
     * Client errors - Too Many Requests
     *
     * The user has sent too many requests in a given amount of time. Intended for use with rate-limiting schemes.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function tooManyRequests($message = 'Too Many Requests', $errors = null)
    {
        return $this->errors(429, $message, $errors);
    }


    /**
     * Server error - Internal Server Error
     *
     * A generic error message, given when an unexpected condition was encountered and no more specific message is
     * suitable.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function internalServerError($message = 'Internal Server Error', $errors = null)
    {
        return $this->errors(500, $message, $errors);
    }


    /**
     * Server error - Not Implemented
     *
     * The server either does not recognize the request method, or it lacks the ability to fulfill the request. Usually
     * this implies future availability (e.g., a new feature of a web-service API).
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function notImplemented($message = 'Not Implemented', $errors = null)
    {
        return $this->errors(501, $message, $errors);
    }


    /**
     * Server error - Bad Gateway
     *
     * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function badGateway($message = 'Bad Gateway', $errors = null)
    {
        return $this->errors(502, $message, $errors);
    }


    /**
     * Server error - Service Unavailable
     *
     * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a
     * temporary state.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function serviceUnavailable($message = 'Service Unavailable', $errors = null)
    {
        return $this->errors(503, $message, $errors);
    }


    /**
     * Server error - Gateway Time-out
     *
     * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function gatewayTimeOut($message = 'Gateway Time-out', $errors = null)
    {
        return $this->errors(504, $message, $errors);
    }


    /**
     * Server error - HTTP Version Not Supported
     *
     * The server does not support the HTTP protocol version used in the request.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function httpVersionNotSupported($message = 'HTTP Version Not Supported', $errors = null)
    {
        return $this->errors(505, $message, $errors);
    }


    /**
     * Server error - Variant Also Negotiates
     *
     * Transparent content negotiation for the request results in a circular reference.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function variantAlsoNegotiates($message = 'Variant Also Negotiates', $errors = null)
    {
        return $this->errors(506, $message, $errors);
    }


    /**
     * Server error - Insufficient Storage
     *
     * The server is unable to store the representation needed to complete the request.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function insufficientStorage($message = 'Insufficient Storage', $errors = null)
    {
        return $this->errors(507, $message, $errors);
    }


    /**
     * Server error - Loop Detected
     *
     * The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function loopDetected($message = 'Loop Detected', $errors = null)
    {
        return $this->errors(508, $message, $errors);
    }


    /**
     * Server error - Not Extended
     *
     * Further extensions to the request are required for the server to fulfill it.
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function notExtended($message = 'Not Extended', $errors = null)
    {
        return $this->errors(510, $message, $errors);
    }


    /**
     * Server error - Network Authentication Required
     *
     * The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to
     * control access to the network (e.g., "captive portals" used to require agreement to Terms of Service before
     * granting full Internet access via a Wi-Fi hotspot).
     *
     * @param string $message
     * @param null   $errors
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function networkAuthenticationRequired($message = 'Network Authentication Required', $errors = null)
    {
        return $this->errors(511, $message, $errors);
    }


    /**
     * Parameters Validator.
     *
     * @param      $payload
     * @param      $rules
     * @param null $message
     *
     * @return mixed
     * @throws SongshenzongException
     */
    public function validator($payload, $rules, $message = null)
    {
        if (is_array($payload)) {
            $validator = app('validator')->make($payload, $rules);
        } else {
            if (method_exists($payload, 'all')) {
                $validator = app('validator')->make($payload->all(), $rules);
            } else {
                return $this->internalServerError('The first argument must be an array.');
            }
        }

        $status_code = env('SONGSHENZONG_API_VALIDATOR_HTTP_STATUS_CODE', 422);

        if (null === $message) {
            $message = env('SONGSHENZONG_API_VALIDATOR', 'Unprocessable Entity');
        }

        if ($validator->fails()) {
            return $this->setHttpStatusCode($status_code)
                        ->unprocessableEntity($message, $validator->errors());
        }

        return true;
    }
}
