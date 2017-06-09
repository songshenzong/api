<?php

namespace Songshenzong\Api;

/**
 * Class Facade
 *
 * @package Songshenzong\Api
 */
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
