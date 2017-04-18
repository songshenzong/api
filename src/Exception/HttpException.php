<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Songshenzong\ResponseJson\Exception;

/**
 * HttpException.
 *
 */
class HttpException extends \RuntimeException implements \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
{
    private $httpStatusCode;
    private $statusCode;
    private $headers;

    public function __construct($httpStatusCode, $statusCode, $message = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
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
}
