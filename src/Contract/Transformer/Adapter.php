<?php

namespace Songshenzong\ResponseJson\Contract\Transformer;

use Songshenzong\ResponseJson\Http\Request;
use Songshenzong\ResponseJson\Transformer\Binding;

interface Adapter
{
    /**
     * Transform a response with a transformer.
     *
     * @param mixed                          $response
     * @param object                         $transformer
     * @param \Songshenzong\ResponseJson\Transformer\Binding $binding
     * @param \Songshenzong\ResponseJson\Http\Request        $request
     *
     * @return array
     */
    public function transform($response, $transformer, Binding $binding, Request $request);
}
