<?php

namespace Songshenzong\ResponseJson\Contract\Routing;

use Illuminate\Http\Request;

interface Adapter
{
    /**
     * Dispatch a request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $version
     *
     * @return mixed
     */
    public function dispatch(Request $request, $version);
}
