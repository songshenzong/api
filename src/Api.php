<?php

namespace Songshenzong\Api;

use Songshenzong\Api\Exception\ApiException;
use Songshenzong\Api\Traits\Errors;
use Songshenzong\Api\Traits\Hypermedia;
use Songshenzong\Api\Traits\Success;
use const true;

/**
 * Class Api
 *
 * @package Songshenzong\Api
 * @mixin Errors
 * @mixin Hypermedia
 * @mixin Success
 */
class Api
{

    use Errors, Success, Hypermedia;
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
    public function getCode()
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
    public function getMessage()
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
    public function getData()
    {
        return $this->data;
    }


    /**
     * @param int $httpStatusCode
     *
     * @return mixed|Api
     * @throws ApiException
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
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }


    /**
     * @param $statusCode
     *
     * @return mixed|Api
     * @throws ApiException
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
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Parameters Validator.
     *
     * @param      $payload
     * @param      $rules
     * @param null $message
     *
     * @return mixed
     * @throws ApiException
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

        $status_code = config('api.validator.status_code', 422);

        if (null === $message) {
            $message = config('api.validator.message', 'Unprocessable Entity');
        }

        if ($validator->fails()) {
            return $this->setHttpStatusCode($status_code)
                        ->unprocessableEntity($message, $validator->errors());
        }

        return true;
    }
}
