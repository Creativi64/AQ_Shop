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

include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/vendor/adodb/adodb-php/drivers/adodb-pdo.inc.php';
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/vendor/adodb/adodb-php/drivers/adodb-pdo_mysql.inc.php';
include_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/vendor/adodb/adodb-php/drivers/adodb-mysqli.inc.php';

class xt_adodb_mysqli extends ADODB_mysqli
{
    public function CacheExecute($secs2cache, $sql = false, $inputarr = false)
    {
        if(_CACHETIME_ADODB == 0)
        {
            if (!is_numeric($secs2cache)) {
                $inputarr = $sql;
                $sql = $secs2cache;
                $secs2cache = $this->cacheSecs;
            }

            if (is_array($sql)) {
                $sqlparam = $sql;
                $sql = $sql[0];
            } else
                $sqlparam = $sql;

            return $this->Execute($sqlparam,$inputarr);
        }
        return parent::CacheExecute($secs2cache, $sql, $inputarr);
    }
}
