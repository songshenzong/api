<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Api\Exception;

use Exception;
use Songshenzong\Api\Api;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


/**
 * Class ApiException
 *
 * @package Songshenzong\Api\Exception
 */
class ApiException extends \RuntimeException implements HttpExceptionInterface
{

    /**
     * MessageBag errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    private $httpStatusCode;
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var null
     */
    protected $errors;
    /**
     * @var array
     */
    private $headers;
    /**
     * @var int
     */
    protected $code;

    /**
     * @var array
     */
    public $Hypermedia;

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
        $this->code           = $api->getStatusCode();
        $this->Hypermedia     = $api->getHypermedia();
        $this->headers        = $headers;

        parent::__construct($api->getMessage(), $this->code, $previous);
    }


    /**
     * @return \Illuminate\Support\MessageBag|string
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        // if (!$this -> httpStatusCode) {
        //     return $this -> statusCode;
        // }
        // if ($this -> httpStatusCode != $this -> statusCode) {
        //     return $this -> httpStatusCode;
        // }

        return $this->statusCode;
    }


    /**
     * @return int
     */
    public function getOriginalStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return null
     */
    public function getErrors()
    {
        return $this->errors;
    }


    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }


    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }
}
