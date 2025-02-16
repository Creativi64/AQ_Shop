<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');


/**
 * Class xt_cache
 *
 * de/serialisiert Arrays
 * Aufrufe auf nicht-Arrays sollen false erzeugen, bzw werfen Laufzeitfehler
 *
 */
class xt_cache
{
    protected static $_cache_dir = _SRV_WEBROOT.'cache/';
    protected static $_prefix = '_cache_xt';
    protected static $_suffix = 'ser';

    protected function __construct(){}

    public static function getCache($name, array $props = [])
    {
        $data = false;

        if(!empty($name))
        {
            $file = self::getFile($name, $props);

            if (file_exists($file))
            {
                $s = file_get_contents($file);
                $data = unserialize($s);
                unset($s);
            }
        }

        return $data;
    }

    public static function setCache(array $data, $name, array $props = [])
    {
        if(empty($name)) return false;

        $file = self::getFile($name, $props);

        $s = serialize($data);
        file_put_contents($file, $s);
        unset($s);
    }

    public static function deleteCache($which = '')
    {
        $path = self::$_cache_dir.self::$_prefix;
        if(!empty($which))
        {
            $path .= '.'.$which;
        }

        array_map('unlink', glob($path.'*'));
    }

    protected static function getFile($name, $props = [])
    {
        $file = self::$_cache_dir.self::$_prefix.'.'.$name;

        if(count($props))
        {
            $file.= '.';

            foreach($props as $k => $v)
            {
                if($v === 'true') $v = '1';
                else if($v === 'false') $v = '0';
                $file.= '_'.$k.'-'.$v;
            }
        }
        $file.= '.'.self::$_suffix;

        return $file;
    }

}
