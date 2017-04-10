<?php

if (!function_exists('httpJson')) {
    /**
     * Get the instance
     *
     * @return \Songshenzong\HttpJson\HttpJson
     */
    function httpJson()
    {
        return app('HttpJson');
    }
}