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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/classes/constants.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/functions.php';

global $db, $store_handler, $language;

GLOBAL $ADODB_THROW_EXCEPTIONS;
$ate = $ADODB_THROW_EXCEPTIONS;
$ADODB_THROW_EXCEPTIONS = true;
$errors = [];

$settings = $db->GetArray("SELECT * FROM ".TABLE_CONFIGURATION_PAYMENT." where config_key like 'XT_PAYPAL_CHECKOUT_%_ORDER_STATUS_CREATED' group by config_key, payment_id order by config_key");
foreach ($settings as $setting)
{
    $setting['config_key'] = str_replace('ORDER_STATUS_CREATED', 'ORDER_STATUS_NEW', $setting['config_key']);

    $exits = $db->GetOne('SELECT config_key FROM '.TABLE_CONFIGURATION_PAYMENT. ' WHERE config_key = ? AND shop_id = ?', [$setting['config_key'], $setting['shop_id']]);

    if(!$exits)
    {
        $setting['config_value'] = 16;
        $setting['sort_order'] = 4;

        try
        {
            $oP = new adminDB_DataSave(TABLE_CONFIGURATION_PAYMENT, $setting, false, 'payment');
            $objP = $oP->saveDataSet();
        } catch (Exception $e)
        {
            $errors[] = $e->getMessage();
        }
    }
}
$ADODB_THROW_EXCEPTIONS = $ate;

if (!$this->_FieldExists('ppcp_express', TABLE_ORDERS))
{
    $db->Execute("ALTER TABLE `" . TABLE_ORDERS . "` ADD COLUMN `ppcp_express` TINYINT(1) NULL DEFAULT 0 ,
           ADD INDEX `idx_ppcp_express` (`ppcp_express`) ;");
}

/** @var $output string */
if (count($errors))
{
    $output .= "<div style='border:1px solid #25211d; background:#cdbc66;padding:10px;'>".constant('TEXT_ALERT').'<br />';
    foreach($errors as $warning)
    {
        $output .=  $warning. '<br />';
    }
    $output .=  "</div><br>";
}
