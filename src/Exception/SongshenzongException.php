<?php

/*
 * This file is part of the Symfony package.
 *
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\Api\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * {@inheritDoc}
 */

/**
 * Class SongshenzongException
 *
 * @package Songshenzong\Api\Exception
 */
class SongshenzongException extends \RuntimeException implements HttpExceptionInterface
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
     * {@inheritDoc}
     */
    /**
     * SongshenzongException constructor.
     *
     * @param string         $httpStatusCode
     * @param int            $statusCode
     * @param null           $message
     * @param null           $errors
     * @param int            $code
     * @param Exception|null $previous
     * @param array          $headers
     */
    public function __construct($httpStatusCode, $statusCode, $message = null, $errors = null, $code = 0, Exception $previous = null, $headers = [])
    {


        $this->httpStatusCode = $httpStatusCode;
        $this->statusCode     = $statusCode;
        $this->errors         = $errors;
        $this->headers        = $headers;
        $this->code           = $code;

        parent::__construct($message, $code, $previous);
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
