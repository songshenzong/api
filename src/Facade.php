<?php

namespace Songshenzong\Api;

class Facade extends \Illuminate\Support\Facades\Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'SongshenzongApi';
    }
}
