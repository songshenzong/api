<?php

use Illuminate\Http\JsonResponse;

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

if (!function_exists('ok')) {
    /**
     * @param null $data
     *
     * @return JsonResponse
     */
    function ok($data = null)
    {
        return api()->ok($data);
    }
}
