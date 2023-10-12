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

// -- auto loader
spl_autoload_register(function ($class)
{
    global $is_pro_version, $_SYSTEM_INSTALL_SUCCESS;

    if(is_null($is_pro_version))
    {
        $is_pro_version = false;
        require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'functions/read_license.php';

        $lic_info = getLicenseFileInfo(['versiontype']);
        $die_msg = false;
        if (is_array($lic_info))
        {
            if ($lic_info['versiontype'] == 'PRO')
            {
                $is_pro_version = true;
            }
            elseif ($lic_info['versiontype'] != 'FREE')
            {
                $die_msg = ' - license error - lic11';
            }
        }
        else
        {
            $die_msg = ' - license error - lic12';
        }
        if($_SYSTEM_INSTALL_SUCCESS === 'true' && !empty($die_msg))
        {
            die($die_msg);
        }

    }
    if($is_pro_version)
    {
        $class_file = _SRV_WEBROOT.'xtPro/'._SRV_WEB_FRAMEWORK.'classes/class.' . $class . '.php';
        if (file_exists($class_file))
        {
            include_once $class_file;
        }
        else
        {
            $class_file = _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.' . $class . '.php';
            if (file_exists($class_file))
            {
                include_once $class_file;
            }
        }
    }
    else
    {
        $class_file = _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.' . $class . '.php';
        if (file_exists($class_file))
        {
            include_once $class_file;
        }
    }
});
