<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (!function_exists('array_value'))
{
    function array_value($arr, $key, $default = null)
    {
        if(!is_array($arr)
            || !array_key_exists($key, $arr))
        {
            return $default;
        }
        return $arr[$key];
    }
}