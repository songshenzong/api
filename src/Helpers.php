<?php

if (!function_exists('songshenzongApi')) {
    /**
     * Get the instance
     *
     */
    function songshenzongApi()
    {

        return clone app('SongshenzongApi');
    }
}


if (!function_exists('api')) {
    /**
     * Get the instance
     *
     */
    function api()
    {
        return clone app('SongshenzongApi');
    }
}
