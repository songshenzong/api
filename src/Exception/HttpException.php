<?php

/*
 * This file is part of the Symfony package.
 *
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\ResponseJson\Exception;

use Illuminate\Support\MessageBag;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HttpException extends \RuntimeException implements HttpExceptionInterface
{

    /**
     * MessageBag errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;
    private   $httpStatusCode;
    private   $statusCode;
    private   $headers;

    public function __construct($httpStatusCode, $statusCode, $message = null, $errors = null, Exception $previous = null, $headers = [], $code = 0)
    {
        if (is_null($errors)) {
            $this -> errors = new MessageBag;
        } else {
            $this -> errors = is_array($errors) ? new MessageBag($errors) : $errors;
        }


        $this -> httpStatusCode = $httpStatusCode;
        $this -> statusCode     = $statusCode;
        $this -> headers        = $headers;


        parent ::__construct($message, $code, $previous);
    }


    public function getHttpStatusCode()
    {
        return $this -> httpStatusCode;
    }

    public function getStatusCode()
    {
        return $this -> statusCode;
    }

    public function getHeaders()
    {
        return $this -> headers;
    }


    public function getErrors()
    {
        return $this -> errors;
    }


    public function hasErrors()
    {
        return !empty($this -> errors);
    }

}
