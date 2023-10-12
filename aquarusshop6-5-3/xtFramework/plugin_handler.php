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

global $xtPlugin, $is_pro_version, $db;

//require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.hookpoint.php';
$xtPlugin = new hookpoint();

//--------------------------------------------------------------------------------------
// do PRO/FREE checks/updates
//--------------------------------------------------------------------------------------
if(!$is_pro_version)
{
    // activate kco plg
    if(!array_key_exists('xt_klarna_kco', $xtPlugin->active_modules))
    {
        try
        {
            $plg_id = $db->GetOne("SELECT plugin_id FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE code=?", array('xt_klarna_kco'));
            if ($plg_id)
            {
                $plg_installed = new plugin_installed();
                $plg_installed->_setStatus($plg_id, true);
            }
            else if (USER_POSITION == 'admin')
            {
                $c = $_SESSION['install_kco_warning_count'];
                if(empty($c)) $c = 0;
                if($c%20 == 0)
                {
                    global $logHandler;
                    if (empty($logHandler))
                    {
                        $logHandler = new LogHandler();
                    }
                    $logHandler->_addPopupNotification('error', 'xt_klarna_kco', 0,
                        [
                            'msg' => [
                                'de' => 'Sie verwenden XT Free.<br />Bitte installieren Sie das Plugin xt_klarna_kco',
                                'en' => 'You are using XT Free.<br />Please install plugin xt_klarna_kco'
                            ]
                        ]);
                }
                $_SESSION['install_kco_warning_count'] = $c+1;
            }
        } catch(Exception $e)
        {
            error_log($e->getMessage());
        }
    }
}
else if ($is_pro_version === true){
    // deactivate kco plg
    if(array_key_exists('xt_klarna_kco', $xtPlugin->active_modules))
    {
        try
        {
            $plg_id = $db->GetOne("SELECT plugin_id FROM " . TABLE_PLUGIN_PRODUCTS . " WHERE code=?", array('xt_klarna_kco'));
            if ($plg_id)
            {
                $plg_installed = new plugin_installed();
                $plg_installed->_setStatus($plg_id, false);
            }
        } catch(Exception $e)
        {
            error_log($e->getMessage());
        }
    }
}

//--------------------------------------------------------------------------------------
// Loading Pre Includes Tab
//--------------------------------------------------------------------------------------
($plugin_code = $xtPlugin->PluginCode('_pre_include')) ? eval($plugin_code) : false;
