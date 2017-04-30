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

class SongshenzongException extends \RuntimeException implements HttpExceptionInterface
{

    /**
     * MessageBag errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    private $httpStatusCode;
    private $statusCode;
    protected $errors;
    private $headers;
    protected $code;

    public function __construct($httpStatusCode, $statusCode, $message = null, $errors = null, $code = 0, Exception $previous = null, $headers = [])
    {


        $this -> httpStatusCode = $httpStatusCode;
        $this -> statusCode     = $statusCode;
        $this -> errors         = $errors;
        $this -> headers        = $headers;
        $this -> code           = $code;

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


    public function getErrors()
    {
        return $this -> errors;
    }


    public function getHeaders()
    {
        return $this -> headers;
    }


    public function hasErrors()
    {
        return !empty($this -> errors);
    }
}
