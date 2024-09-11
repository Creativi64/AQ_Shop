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

include '../xtFramework/admin/main.php';

global $xtc_acl;

if (!$xtc_acl->isLoggedIn())
{
    die('login required');
}

if (isset($_GET['module']))
{

    $json_response = versionCheck();
    if ($json_response['message'] != '')
    {
        echo $json_response['message'];
    }
    else
    {
        if ($json_response['core_not_actual'] == 1)
        {
            $target_version = $json_response['actual_version'];
            echo sprintf(__text('TEXT_UPDATE_YES'), _SYSTEM_VERSION, $target_version);
            if ($json_response['update_link'] != '')
            {
                echo ' <a href="' . $json_response['update_link'] . '" target="_blank">Link</a>';
            }
        }
        else
        {
            echo sprintf(__text('TEXT_UPDATE_NO'), _SYSTEM_VERSION);
        }

        if (is_countable($json_response) && is_countable($json_response['plugins']) && count($json_response['plugins']) > 0)
        {
            echo "<br>";
            foreach ($json_response['plugins'] as $key => $val)
            {
                echo __text('TEXT_UPDATE_PLUGIN_LOCAL_VERSION') . '<br>' . $val['code'] . '-' . $json_response['plugins'][$val['code']] . ' ' . __text('TEXT_PLUGINVERSION_AVAILABLE') . ' ' . $val['version'] . "<br>";
            }
        }

    }
}
