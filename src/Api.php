<?php

namespace Songshenzong\Api;

use Songshenzong\Api\Exception\ApiException;
use Songshenzong\Api\Traits\Errors;
use Songshenzong\Api\Traits\Hypermedia;
use Songshenzong\Api\Traits\Success;
use Illuminate\Http\Response;

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
    protected $message = '';

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @var int|null
     */
    protected $httpStatusCode;

    /**
     * @var int|null
     */
    protected $api_code;


    /**
     * @param int $code
     *
     * @return Api
     */
    public function setCode(int $code): self
    {
        $this->api_code = $code;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getCode()
    {
        return $this->api_code;
    }

    /**
     * @param string $message
     *
     * @return Api
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * @param int $httpStatusCode
     *
     * @return $this|void
     * @throws ApiException
     */
    public function setHttpStatusCode(int $httpStatusCode)
    {
        if (!array_key_exists($httpStatusCode, Response::$statusTexts)) {
            return $this->internalServerError('Invalid status code in ' . __METHOD__, Response::$statusTexts);
        }
        $this->httpStatusCode = $httpStatusCode;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }


    /**
     * @param int $statusCode
     *
     * @return $this|void
     * @throws ApiException
     */
    public function setStatusCode(int $statusCode)
    {
        if (!array_key_exists($statusCode, Response::$statusTexts)) {
            return $this->internalServerError('Invalid status code in ' . __METHOD__, Response::$statusTexts);
        }

        $this->statusCode = $statusCode;

        return $this;
    }


    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

}
