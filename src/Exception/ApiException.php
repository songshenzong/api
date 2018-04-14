<?php

namespace Songshenzong\Api\Exception;

use Exception;
use RuntimeException;
use Songshenzong\Api\Api;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ApiException
 *
 * @package Songshenzong\Api\Exception
 */
class ApiException extends RuntimeException implements HttpExceptionInterface
{

    /**
     * @var Api
     */
    public $apiMessage;


    /**
     * ApiException constructor.
     *
     * @param Api            $api
     * @param Exception|null $previous
     */
    public function __construct(Api $api, Exception $previous = null)
    {
        $this->apiMessage = $api;
        parent::__construct($api->getMessage(), $api->getCode(), $previous);
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->apiMessage->getHeaders();
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->apiMessage->getStatusCode();
    }
}
