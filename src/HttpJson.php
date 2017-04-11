<?php

namespace Songshenzong\HttpJson;

use Dingo\Api\Routing\Helpers;

use Symfony\Component\HttpKernel\Exception\HttpException;


class HttpJson
{
    use Helpers;

    /**
     * Basic Json
     *
     * @param string $message
     * @param int    $status_code
     * @param null   $data
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function json($message = 'OK', $status_code = 200, $data = null)
    {
        $result = [
            'status_code' => $status_code,
            'message'     => $message,
        ];
        if ($data != null) {
            $result['data'] = $data;
        }

        /**
         * 应该用 Return 而不是 Exit，由于一开始设计好，这里有坑，改动前一定要问帅疆
         */
        if ($status_code != 200) {
            header("Content-type: application/json");
            echo json_encode($result);
            exit;
        }


        return \response() -> json($result);
    }

    /**
     * OK - Success
     *
     * Standard response for successful HTTP requests. The actual response will depend on the request method used. In a
     * GET request, the response will contain an entity corresponding to the requested resource. In a POST request, the
     * response will contain an entity describing or containing the result of the action.[7]
     *
     * @param array $data
     *
     * @return mixed
     */
    public function ok($data = [])
    {
        return $this -> json('OK', 200, $data);
    }

    /**
     * Created - Success
     *
     * The request has been fulfilled, resulting in the creation of a new resource.[8]
     *
     * @param array $data
     *
     * @return mixed
     */
    public function created($data = [])
    {
        return $this -> json('Created', 201, $data);
    }

    /**
     * Accepted - Success
     *
     * The request has been accepted for processing, but the processing has not been completed. The request might or
     * might not be eventually acted upon, and may be disallowed when processing occurs.[9]
     *
     * @param array $data
     *
     * @return mixed
     */
    public function accepted($data = [])
    {
        return $this -> json('Accepted', 202, $data);
    }

    /**
     * No Content - Success
     *
     * The server successfully processed the request and is not returning any content.[12]
     *
     * @param array $data
     *
     * @return mixed
     */
    public function noContent($data = [])
    {
        return $this -> json('No Content', 204, $data);
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
    public function error($message, $statusCode)
    {
        throw new HttpException($statusCode, $message);
    }


    /**
     * Bad Request - Client errors
     *
     * The server cannot or will not process the request due to an apparent client error (e.g., malformed request
     * syntax, too large size, invalid request message framing, or deceptive request routing).
     *
     * @param string $message
     *
     * @return mixed
     */
    public function badRequest($message = 'Bad Request', $status_code = 400, $data = null)
    {
        return $this -> json($message, $status_code, $data);
    }

    /**
     * Unauthorized - Client errors
     *
     * Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet
     * been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to
     * the requested resource. See Basic access authentication and Digest access authentication.[33] 401 semantically
     * means "unauthenticated",[34] i.e. the user does not have the necessary credentials. Note: Some sites issue HTTP
     * 401 when an IP address is banned from the website (usually the website domain) and that specific address is
     * refused permission to access a website.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function unauthorized($message = 'Unauthorized', $status_code = 401, $data = null)
    {
        return $this -> json($message, $status_code, $data);
    }

    /**
     * Forbidden - Client errors
     *
     * The request was valid, but the server is refusing action. The user might not have the necessary permissions for
     * a resource.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function forbidden($message = 'Forbidden', $status_code = 403, $data = null)
    {
        return $this -> json($message, $status_code, $data);
    }

    /**
     * Not Found - Client errors
     *
     * The requested resource could not be found but may be available in the future. Subsequent requests by the client
     * are permissible.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function notFound($message = 'Not Found', $status_code = 404, $data = null)
    {
        return $this -> json($message, $status_code, $data);
    }

    /**
     * Gone - Client errors
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
    public function gone($message = 'Gone', $status_code = 410, $data = null)
    {
        return $this -> json($message, $status_code, $data);
    }

    /**
     * Unprocessable Entity - Client errors
     *
     * The request was well-formed but was unable to be followed due to semantic errors.[15].
     *
     * @param string $message
     *
     * @return mixed
     */
    public function unprocessableEntity($message = 'Unprocessable Entity', $status_code = 422, $data = null)
    {
        return $this -> json($message, $status_code, $data);
    }

    /**
     * Internal Server Error - Server error
     *
     * A generic error message, given when an unexpected condition was encountered and no more specific message is
     * suitable.[57]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function internalServerError($message = 'Internal Server Error', $data = null)
    {
        return $this -> json($message, 500, $data);
    }

    /**
     * Not Implemented - Server error
     *
     * The server either does not recognize the request method, or it lacks the ability to fulfill the request. Usually
     * this implies future availability (e.g., a new feature of a web-service API).[58]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function notImplemented($message = 'Not Implemented', $data = null)
    {
        return $this -> json($message, 501, $data);
    }

    /**
     * Bad Gateway - Server error
     *
     * The server was acting as a gateway or proxy and received an invalid response from the upstream server.[59]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function badGateway($message = 'Bad Gateway', $data = null)
    {
        return $this -> json($message, 502, $data);
    }

    /**
     * Service Unavailable - Server error
     *
     * The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a
     * temporary state.[60]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function serviceUnavailable($message = 'Service Unavailable', $data = null)
    {
        return $this -> json($message, 503, $data);
    }

    /**
     * Gateway Time-out - Server error
     *
     * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.[61]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function gatewayTimeOut($message = 'Gateway Time-out', $data = null)
    {
        return $this -> json($message, 504, $data);
    }

    /**
     * HTTP Version Not Supported - Server error
     *
     * The server does not support the HTTP protocol version used in the request.[62]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function httpVersionNotSupported($message = 'HTTP Version Not Supported', $data = null)
    {
        return $this -> json($message, 505, $data);
    }

    /**
     * Variant Also Negotiates - Server error
     *
     * Transparent content negotiation for the request results in a circular reference.[63]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function variantAlsoNegotiates($message = 'Variant Also Negotiates', $data = null)
    {
        return $this -> json($message, 506, $data);
    }

    /**
     * Insufficient Storage - Server error
     *
     * The server is unable to store the representation needed to complete the request.[15]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function insufficientStorage($message = 'Insufficient Storage', $data = null)
    {
        return $this -> json($message, 507, $data);
    }

    /**
     * Loop Detected - Server error
     *
     * The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function loopDetected($message = 'Loop Detected', $data = null)
    {
        return $this -> json($message, 508, $data);
    }

    /**
     * Not Extended - Server error
     *
     * Further extensions to the request are required for the server to fulfill it.[64]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function notExtended($message = 'Not Extended', $data = null)
    {
        return $this -> json($message, 510, $data);
    }

    /**
     * Network Authentication Required - Server error
     *
     * The client needs to authenticate to gain network access. Intended for use by intercepting proxies used to
     * control access to the network (e.g., "captive portals" used to require agreement to Terms of Service before
     * granting full Internet access via a Wi-Fi hotspot).[53]
     *
     * @param string $message
     * @param array  $data
     *
     * @return mixed
     */
    public function networkAuthenticationRequired($message = 'Network Authentication Required', $data = null)
    {
        return $this -> json($message, 511, $data);
    }
}
