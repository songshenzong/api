<?php

if (!function_exists('responseJson')) {
    /**
     * Get the instance
     *
     * @return \Songshenzong\ResponseJson\ResponseJson
     */
    function responseJson()
    {
        return app('ResponseJson');
    }
}
