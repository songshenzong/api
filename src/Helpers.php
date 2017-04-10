<?php

if (!function_exists('songshenzong')) {
    /**
     * Get the instance
     *
     * @return \Songshenzong\Log\LaravelDebugbar
     */
    function songshenzong()
    {
        return app('songshenzong');
    }
}

if (!function_exists('debug')) {
    /**
     * Adds one or more messages to the MessagesCollector
     *
     * @param  mixed ...$value
     * @return string
     */
    function debug($value)
    {
        $debug = app('songshenzong');
        foreach (func_get_args() as $value) {
            $debug->addMessage($value, 'debug');
        }
    }
}

if (!function_exists('start_measure')) {
    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     */
    function start_measure($name, $label = null)
    {
        app('songshenzong')->startMeasure($name, $label);
    }
}

if (!function_exists('stop_measure')) {
    /**
     * Stop a measure
     *
     * @param string $name Internal name, used to stop the measure
     */
    function stop_measure($name)
    {
        app('songshenzong')->stopMeasure($name);
    }
}

if (!function_exists('add_measure')) {
    /**
     * Adds a measure
     *
     * @param string $label
     * @param float $start
     * @param float $end
     */
    function add_measure($label, $start, $end)
    {
        app('songshenzong')->addMeasure($label, $start, $end);
    }
}

if (!function_exists('measure')) {
    /**
     * Utility function to measure the execution of a Closure
     *
     * @param string $label
     * @param \Closure $closure
     */
    function measure($label, \Closure $closure)
    {
        app('songshenzong')->measure($label, $closure);
    }
}
