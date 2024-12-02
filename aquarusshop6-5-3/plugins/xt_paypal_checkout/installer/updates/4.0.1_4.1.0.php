<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/classes/constants.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_paypal_checkout/installer/functions.php';

global $db;

/** @var $this plugin */
/** @var $plugin array */
/** @var $product_id int */
/** @var $_plugin_code string */

if(!$this->_FieldExists('ap_ppcp_order_id', TABLE_ADDITIONAL_PAYMENTS))
{

    $db->Execute("ALTER TABLE ".TABLE_ADDITIONAL_PAYMENTS." ADD COLUMN `ap_ppcp_order_id` VARCHAR(32) NULL DEFAULT NULL;");
    $a = $db->GetOne("SHOW INDEX FROM ".TABLE_ADDITIONAL_PAYMENTS." WHERE KEY_NAME = 'idx_ppcp_order_id'");
    if(empty($a))
        $db->Execute("ALTER TABLE ".TABLE_ADDITIONAL_PAYMENTS." ADD INDEX `idx_ppcp_order_id` (`ap_ppcp_order_id`) ;");

}

if (!$this->_FieldExists('ap_ppcp_order_status', TABLE_ADDITIONAL_PAYMENTS))
{
    $db->Execute("ALTER TABLE `" . TABLE_ADDITIONAL_PAYMENTS . "` ADD COLUMN `ap_ppcp_order_status` VARCHAR(32) NULL DEFAULT NULL;");
    $a = $db->GetOne("SHOW INDEX FROM " . TABLE_ADDITIONAL_PAYMENTS . " WHERE KEY_NAME = 'idx_ppcp_order_status'");
    if (empty($a))
    {
        $db->Execute("ALTER TABLE `" . TABLE_ADDITIONAL_PAYMENTS . "` ADD INDEX `idx_ppcp_order_status` (`ap_ppcp_order_status`);");
    }
}
