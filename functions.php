<?php

if (!function_exists('array_flatten')) {
    /**
     * @param array[] $array
     * @return array
     */
    function array_flatten(array $array)
    {
        $return = [];

        array_walk_recursive($array, static function ($x) use (&$return) {
            $return[] = $x;
        });

        return $return;
    }
}

if (!function_exists('loop')) {
    /**
     * @param callable $handle
     * @param integer $count
     */
    function loop(callable $handle, int $count)
    {
        if ($count > 0) {
            foreach (range(1, $count) as $i) {
                if ($handle($i) === false) {
                    break;
                }
            }
        }
    }
}

if (!function_exists('random_color')) {
    function random_color()
    {
        return sprintf('#%06X', random_int(0, 0xFFFFFF));
    }
}