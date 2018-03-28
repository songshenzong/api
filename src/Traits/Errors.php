<?php

namespace Songshenzong\Api\Traits;

use Songshenzong\Api\Exception\ApiException;

trait Errors
{
    /**
     * @var
     */
    protected $errors;

    /**
     * Public Errors Exception Method.
     *
     * @param      $statusCode
     * @param      $message
     * @param null $errors
     *
     * @throws ApiException
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

        throw new ApiException($this);
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
    public function getErrors()
    {
        return $this->errors;
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
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
     * @throws ApiException
     */
    public function networkAuthenticationRequired($message = 'Network Authentication Required', $errors = null)
    {
        return $this->errors(511, $message, $errors);
    }
}