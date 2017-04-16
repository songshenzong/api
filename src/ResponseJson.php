<?php

namespace Songshenzong\ResponseJson;


class ResponseJson
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
     * @return int
     */
    public function getStatusCode()
    {
        return $this -> statusCode;
    }


    /**
     * Send Responses
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send()
    {
        $this -> content = [
            'message'     => $this -> message,
            'status_code' => $this -> statusCode,
        ];

        if (!is_null($this -> getData())) {
            $this -> content['data'] = $this -> getData();
        }

        if (!is_null($this -> getErrors())) {
            $this -> content['errors'] = $this -> getErrors();
        }


        return \Response ::json($this -> content, $this -> getStatusCode());
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

        if (is_null($this -> getMessage())) {
            $this -> setMessage('OK');
        }


        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(200);
        }


        if (is_null($this -> getData())) {
            $this -> setData($data);
        }

        return $this -> send();
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


        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(201);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getData())) {
            $this -> setData($data);
        }

        return $this -> send();
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

        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(202);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getData())) {
            $this -> setData($data);
        }

        return $this -> send();
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


        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(203);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getData())) {
            $this -> setData($data);
        }

        return $this -> send();
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

        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(204);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getData())) {
            $this -> setData($data);
        }

        return $this -> send();
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
    public function resetContent($data = null)
    {


        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(205);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage('Reset Content');
        }

        if (is_null($this -> getData())) {
            $this -> setData($data);
        }

        return $this -> send();
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

        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(400);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }


    /**
     * @param string $message
     * @param null   $errors
     */
    public function badRequestException($message = 'Bad Request', $errors = null)
    {
        throw new ResourceException(400, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(401);
        }

        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function unauthorizedException($message = 'Unauthorized', $errors = null)
    {
        throw new ResourceException(401, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(403);
        }

        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function forbiddenException($message = 'Forbidden', $errors = null)
    {
        throw new ResourceException(403, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(404);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function notFoundException($message = 'Not Found', $errors = null)
    {
        throw new ResourceException(404, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(405);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function methodNotAllowedException($message = 'Method Not Allowed', $errors = null)
    {
        throw new ResourceException(405, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(406);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function notAcceptableException($message = 'Not Acceptable', $errors = null)
    {
        throw new ResourceException(406, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(409);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function conflictException($message = 'Conflict', $errors = null)
    {
        throw new ResourceException(409, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(410);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function goneException($message = 'Gone', $errors = null)
    {
        throw new ResourceException(410, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(411);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function lengthRequiredException($message = 'Length Required', $errors = null)
    {
        throw new ResourceException(411, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(412);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function preconditionFailedException($message = 'Precondition Failed', $errors = null)
    {
        throw new ResourceException(412, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(413);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function unsupportedMediaTypeException($message = 'Unsupported Media Type', $errors = null)
    {
        throw new ResourceException(413, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(422);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function unprocessableEntityException($message = 'Unprocessable Entity', $errors = null)
    {
        throw new ResourceException(422, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(428);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function preconditionRequiredException($message = 'Precondition Required', $errors = null)
    {
        throw new ResourceException(428, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(429);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function tooManyRequestsException($message = 'Too Many Requests', $errors = null)
    {
        throw new ResourceException(429, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(500);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }


    public function internalServerErrorException($message = 'Internal Server Error', $errors = null)
    {
        throw new ResourceException(500, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(501);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function notImplementedException($message = 'Not Implemented', $errors = null)
    {
        throw new ResourceException(501, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(502);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }


    public function badGatewayException($message = 'Bad Gateway', $errors = null)
    {
        throw new ResourceException(502, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(503);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function serviceUnavailableException($message = 'Service Unavailable', $errors = null)
    {
        throw new ResourceException(503, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(504);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function gatewayTimeOutException($message = 'Gateway Time-out', $errors = null)
    {
        throw new ResourceException(504, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(505);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function httpVersionNotSupportedException($message = 'HTTP Version Not Supported', $errors = null)
    {
        throw new ResourceException(505, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(506);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function variantAlsoNegotiatesException($message = 'Variant Also Negotiates', $errors = null)
    {
        throw new ResourceException(506, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(507);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function insufficientStorageException($message = 'Insufficient Storage', $errors = null)
    {
        throw new ResourceException(507, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(508);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }


    public function loopDetectedException($message = 'Loop Detected', $errors = null)
    {
        throw new ResourceException(508, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(510);
        }


        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function notExtendedException($message = 'Not Extended', $errors = null)
    {
        throw new ResourceException(510, $message, $errors);
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
        if (is_null($this -> getStatusCode())) {
            $this -> setStatusCode(511);
        }

        if (is_null($this -> getMessage())) {
            $this -> setMessage($message);
        }

        if (is_null($this -> getErrors())) {
            $this -> setErrors($errors);
        }

        return $this -> send();
    }

    public function networkAuthenticationRequiredException($message = 'Network Authentication Required', $errors = null)
    {
        throw new ResourceException(511, $message, $errors);
    }

}
