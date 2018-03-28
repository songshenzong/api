<?php

if (!function_exists('songshenzongApi')) {
    /**
     * @param null $httpStatusCode
     *
     * @return \Illuminate\Foundation\Application|mixed
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
     * @return \Illuminate\Foundation\Application|mixed
     */
    function api($httpStatusCode = null)
    {
        return songshenzongApi($httpStatusCode);
    }
}
