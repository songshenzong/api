<?php

namespace Songshenzong\ResponseJson\Exception;

use Exception;
use Illuminate\Support\MessageBag;
use Songshenzong\ResponseJson\Contract\Debug\MessageBagErrors;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ResourceException extends HttpException implements MessageBagErrors
{
    /**
     * MessageBag errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Create a new resource exception instance.
     *
     * @param string                               $message
     * @param \Illuminate\Support\MessageBag|array $errors
     * @param \Exception                           $previous
     * @param array                                $headers
     * @param int                                  $code
     *
     * @return void
     */
    public function __construct($httpStatusCode, $statusCode, $message = null, $errors = null, Exception $previous = null, $headers = [], $code = 0)
    {

        if (is_null($errors)) {
            $this -> errors = new MessageBag;
        } else {
            $this -> errors = is_array($errors) ? new MessageBag($errors) : $errors;
        }

        parent ::__construct($statusCode, $message, $previous, $headers, $code);
    }

    /**
     * Get the errors message bag.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this -> errors;
    }

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !$this -> errors -> isEmpty();
    }
}
