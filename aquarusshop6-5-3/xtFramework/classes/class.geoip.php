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
 * Class geoip
 */
class geoip
{
    /**
     * Determine Country code based on IP Address,
     * @param bool $ip
     * @return mixed
     */
    public static function getCountryFromIp($ip = false)
    {
        global $xtPlugin;
        if(!$ip)
        {
            $acl = new acl();
            $ip = $acl->getCurrentIp();
        }

        global $xtPlugin;

        $country = _STORE_COUNTRY;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'getCountryFromIp')) ? eval($plugin_code) : false;

        return $country;

    }

    /**
     * @param bool $ip
     * @return bool|mixed
     */
    public static function getCountry($ip = false)
    {
        $result = self::getCountryFromIp($ip);

        if($result != false && strlen($result)==2) return $result;
        return defined('_STORE_COUNTRY') ? constant('_STORE_COUNTRY') : false;
    }
}