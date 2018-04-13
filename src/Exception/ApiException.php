<?php

namespace Songshenzong\Api\Exception;

use function dd;
use Exception;
use Songshenzong\Api\Api;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use RuntimeException;

/**
 * Class ApiException
 *
 * @package Songshenzong\Api\Exception
 */
class ApiException extends RuntimeException implements HttpExceptionInterface
{

    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $Hypermedia;

    /**
     * ApiException constructor.
     *
     * @param Api            $api
     * @param Exception|null $previous
     * @param array          $headers
     */
    public function __construct(Api $api, Exception $previous = null, $headers = [])
    {
        $this->httpStatusCode = $api->getHttpStatusCode() ?: $api->getStatusCode();
        $this->statusCode     = $api->getStatusCode();
        $this->errors         = $api->getErrors();
        $this->Hypermedia     = $api->getHypermedia();
        $this->headers        = $headers;

        parent::__construct($api->getMessage(), $api->getCode(), $previous);
    }


    /**
     * @return array
     */
    public function getHypermedia(): array
    {
        return $this->Hypermedia;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }


    /**
     * @return array|null|object
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
