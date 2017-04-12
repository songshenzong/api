<?php

namespace Songshenzong\ResponseJson;


use Symfony\Component\HttpKernel\Exception\HttpException;


class ResponseJson
{


    /**
     * Return a new JSON response from the application.
     *
     * @param array $data
     * @param int   $statusCode
     * @param array $headers
     * @param int   $options
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($data = [], $statusCode = 200, array $headers = [], $options = 0)
    {
        return response() -> json($data, $statusCode, $headers, $options);
    }


    /**
     * 2xx Success
     *
     * This class of status codes indicates the action requested by the client was received, understood, accepted, and
     * processed successfully.
     *
     * @param int    $statusCode
     * @param string $message
     * @param null   $data
     *
     * @return mixed
     */
    public function success($statusCode = 200, $message = 'OK', $data = null)
    {
        $content = [
            'status_code' => $statusCode,
            'message'     => $message,
        ];


        if (!is_null($data)) {
            $content['data'] = $data;
        }


        return $this -> json($content, $statusCode);
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
     * Success - Created
     *
     * The request has been fulfilled, resulting in the creation of a new resource.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function created($data = null)
    {
        return $this -> success(201, 'Created', $data);
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
    public function accepted($data = null)
    {
        return $this -> success(202, 'Accepted', $data);
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
    public function nonAuthoritativeInformation($data = null)
    {
        return $this -> success(203, 'Non-Authoritative Information', $data);
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
    public function noContent($data = null)
    {
        return $this -> success(204, 'No Content', $data);
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
        return $this -> success(205, 'Reset Content', $data);
    }


    /**
     * Return an error response.
     *
     * @param string $message
     * @param int    $statusCode
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function error($statusCode, $message)
    {

        throw new HttpException($statusCode, $message);

    }


    /**
     * Client errors - Bad Request
     *
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request
     * syntax, too large size, invalid request message framing, or deceptive request routing).
     *
     * @param string $message
     */
    public function badRequest($message = 'Bad Request')
    {
        return $this -> error(400, $message);
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
    public function unauthorized($message = 'Unauthorized')
    {
        return $this -> error(401, $message);
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
    public function forbidden($message = 'Forbidden')
    {
        return $this -> error(403, $message);
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
    public function notFound($message = 'Not Found')
    {
        return $this -> error(404, $message);
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
    public function methodNotAllowed($message = 'Method Not Allowed')
    {
        return $this -> error(405, $message);
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
    public function notAcceptable($message = 'Not Acceptable')
    {
        return $this -> error(406, $message);
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
    public function conflict($message = 'Conflict')
    {
        return $this -> error(409, $message);
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
    public function gone($message = 'Gone')
    {
        return $this -> error(410, $message);
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
    public function lengthRequired($message = 'Length Required')
    {
        return $this -> error(411, $message);
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
    public function preconditionFailed($message = 'Precondition Failed')
    {
        return $this -> error(412, $message);
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
    public function unsupportedMediaType($message = 'Unsupported Media Type')
    {
        return $this -> error(415, $message);
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
    public function unprocessableEntity($message = 'Unprocessable Entity')
    {
        return $this -> error(422, $message);
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
    public function preconditionRequired($message = 'Precondition Required')
    {
        return $this -> error(428, $message);
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
    public function tooManyRequests($message = 'Too Many Requests')
    {
        return $this -> error(429, $message);
    }

    /**
     * Server error - Internal Server Error
     *
     * A generic error message, given when an unexpected condition was encountered and no more specific message is
     * suitable.
     *
     * @param string $message
     */
    public function internalServerError($message = 'Internal Server Error')
    {
        return $this -> error(500, $message);
    }


    /**
     * Server error - Not Implemented
     *
     * The server either does not recognize the request method, or it lacks the ability to fulfill the request. Usually
     * this implies future availability (e.g., a new feature of a web-service API).
     *
     * @param string $message
     */
    public function notImplemented($message = 'Not Implemented')
    {
        return $this -> error(501, $message);
    }


    /**
     * Server error - Bad Gateway
     *
     * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
     *
     * @param string $message
     */
    public function badGateway($message = 'Bad Gateway')
    {
        return $this -> error(502, $message);
    }


    /**
     * Server error - Service Unavailable
     *
     * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a
     * temporary state.
     *
     * @param string $message
     */
    public function serviceUnavailable($message = 'Service Unavailable')
    {
        return $this -> error(503, $message);
    }


    /**
     * Server error - Gateway Time-out
     *
     * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
     *
     * @param string $message
     */
    public function gatewayTimeOut($message = 'Gateway Time-out')
    {
        return $this -> error(504, $message);
    }


    /**
     * Server error - HTTP Version Not Supported
     *
     * The server does not support the HTTP protocol version used in the request.
     *
     * @param string $message
     */
    public function httpVersionNotSupported($message = 'HTTP Version Not Supported')
    {
        return $this -> error(505, $message);
    }


    /**
     * Server error - Variant Also Negotiates
     *
     * Transparent content negotiation for the request results in a circular reference.
     *
     * @param string $message
     */
    public function variantAlsoNegotiates($message = 'Variant Also Negotiates')
    {
        return $this -> error(506, $message);
    }


    /**
     * Server error - Insufficient Storage
     *
     * The server is unable to store the representation needed to complete the request.
     *
     * @param string $message
     */
    public function insufficientStorage($message = 'Insufficient Storage')
    {
        return $this -> error(507, $message);
    }


    /**
     * Server error - Loop Detected
     *
     * The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
     *
     * @param string $message
     */
    public function loopDetected($message = 'Loop Detected')
    {
        return $this -> error(508, $message);
    }


    /**
     * Server error - Not Extended
     *
     * Further extensions to the request are required for the server to fulfill it.
     *
     * @param string $message
     */
    public function notExtended($message = 'Not Extended')
    {
        return $this -> error(510, $message);
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
    public function networkAuthenticationRequired($message = 'Network Authentication Required')
    {
        return $this -> error(511, $message);
    }


    /**
     * @param $message
     * @param $errors
     */
    public function deleteResourceFailedException($message, $errors)
    {
        throw new ResourceException($message, $errors);
    }

    /**
     * @param $message
     * @param $errors
     */
    public function resourceException($message, $errors)
    {
        throw new ResourceException($message, $errors);
    }

    /**
     * @param $message
     * @param $errors
     */
    public function storeResourceFailedException($message, $errors)
    {
        throw new ResourceException($message, $errors);
    }

    /**
     * @param $message
     * @param $errors
     */
    public function updateResourceFailedException($message, $errors)
    {
        throw new ResourceException($message, $errors);
    }

    /**
     * @param $message
     * @param $errors
     */
    public function validationHttpException($message, $errors)
    {
        throw new ResourceException($message, $errors);
    }

}
