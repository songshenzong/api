<?php

if (!function_exists('songshenzongApi')) {
    /**
     * Get the instance
     *
     */
    function songshenzongApi()
    {
        return app('SongshenzongAPI');
    }
}
