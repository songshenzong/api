<?php

if (!function_exists('songshenzongApi')) {
    /**
     * Get the instance
     *
     */
    function songshenzongApi()
    {
        return app('SongshenzongApi');
    }
}


if (!function_exists('api')) {
    /**
     * Get the instance
     *
     */
    function api()
    {
        return app('SongshenzongApi');
    }
}
