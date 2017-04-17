<?php

namespace Songshenzong\ResponseJson\Contract\Http;

use Illuminate\Http\Request as IlluminateRequest;

interface Request
{
    /**
     * Create a new Dingo request instance from an Illuminate request instance.
     *
     * @param \Illuminate\Http\Request $old
     *
     * @return \Songshenzong\ResponseJson\Http\Request
     */
    public function createFromIlluminate(IlluminateRequest $old);
}
