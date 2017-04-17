<?php

namespace Songshenzong\ResponseJson\Contract\Http\RateLimit;

use Songshenzong\ResponseJson\Http\Request;
use Illuminate\Container\Container;

interface HasRateLimiter
{
    /**
     * Get rate limiter callable.
     *
     * @param \Illuminate\Container\Container $app
     * @param \Songshenzong\ResponseJson\Http\Request         $request
     *
     * @return string
     */
    public function getRateLimiter(Container $app, Request $request);
}
