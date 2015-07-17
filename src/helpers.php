<?php

if (!function_exists('array_is_sequential')) {

    function array_is_sequential($obj)
    {
        $last_key = -1;
        foreach ($obj as $key => $val) {
            if (!is_int($key) || $key < 0 || $key !== $last_key + 1) {
                return false;
            }
            $last_key = $key;
        }
        return true;
    }
}
