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


abstract class CookieType
{
    const FUNCTIONAL     = 'FUNCTIONAL';
    const PREFERENCES    = 'PREFERENCES';
    const ANALYTICS      = 'ANALYTICS';
    const ADVERTISING    = 'ADVERTISING';
    const TRACKING       = 'TRACKING';
    const OTHER          = 'OTHER';

    public static function types()
    {
        static $types = null;
        if(is_null($types))
        {
            $rc = new ReflectionClass(self::class);
            $types = $rc->getConstants();
        }
        return array_keys($types);
    }

    public static function valid(string $ct)
    {
        static $constants = null;
        if(is_null($constants))
        {
            $rc = new ReflectionClass(self::class);
            $constants = $rc->getConstants();
        }
        return array_key_exists(strtoupper($ct), $constants);
    }
}