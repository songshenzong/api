<?php

if (!function_exists('responseJson')) {
    /**
     * Get the instance
     *
     * @return \Songshenzong\HttpJson\HttpJson
     */
    function httpJson()
    {
        return app('ResponseJson');
    }
}