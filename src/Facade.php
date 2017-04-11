<?php

namespace Songshenzong\ResponseJson;

class Facade extends \Illuminate\Support\Facades\Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ResponseJson';
    }
}
