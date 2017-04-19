<?php


namespace Songshenzong\ResponseJson\Contract\Debug;

use Exception;

interface ExceptionHandler
{
    /**
     * Handle an exception.
     *
     */
    public function handle(Exception $exception);
}
