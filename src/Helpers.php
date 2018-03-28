<?php

if (!function_exists('songshenzongApi')) {
    /**
     * @param null $httpStatusCode
     *
     * @return Songshenzong\Api\Api
     */
    function songshenzongApi($httpStatusCode = null)
    {
        if ($httpStatusCode === null) {
            return clone app('SongshenzongApi');
        }
        return clone app('SongshenzongApi')->setHttpStatusCode($httpStatusCode);
    }
}


if (!function_exists('api')) {
    /**
     * @param null $httpStatusCode
     *
     * @return Songshenzong\Api\Api
     */
    function api($httpStatusCode = null)
    {
        return songshenzongApi($httpStatusCode);
    }
}
