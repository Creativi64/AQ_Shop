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

function getLicenseFileInfo($params=array()) {

    if (count($params)==0) return;
    $return=array();
    $_lic = _SRV_WEBROOT . 'lic/license.txt';
    if (!file_exists($_lic) && XT_WIZARD_STARTED !== true)
        die('- xt:Commerce License File missing - lic31');

    if(file_exists($_lic))
    {
        $_file_content = file($_lic);
        foreach ($_file_content as $bline_num => $bline)
        {
            if (strpos($bline, ':') !== FALSE)
            {

                $val_line = substr($bline, 0, strpos($bline, ':'));

                if (in_array($val_line, $params))
                {
                    $return[$val_line] = trim(substr($bline, strpos($bline, ':') + 1));
                }
            }

        }
    }
    return $return;
}