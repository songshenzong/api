<?php

namespace Songshenzong\ResponseJson\Contract\Auth;

use Illuminate\Http\Request;
use Songshenzong\ResponseJson\Routing\Route;

interface Provider
{
    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Songshenzong\ResponseJson\Routing\Route $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route);
}
