<?php

if (!function_exists('songshenzongApi')) {
    /**
     * @param int $httpStatusCode
     *
     * @return Songshenzong\Api\Api
     */
    function songshenzongApi(int $httpStatusCode = null)
    {
        if ($httpStatusCode === null) {
            return clone app('SongshenzongApi');
        }
        return clone app('SongshenzongApi')->setHttpStatusCode($httpStatusCode);
    }
}


if (!function_exists('api')) {
    /**
     * @param int $httpStatusCode
     *
     * @return Songshenzong\Api\Api
     */
    function api(int $httpStatusCode = null)
    {
        return songshenzongApi($httpStatusCode);
    }
}
