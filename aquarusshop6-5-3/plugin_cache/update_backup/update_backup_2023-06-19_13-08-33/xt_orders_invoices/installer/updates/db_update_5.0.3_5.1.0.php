<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

global $db;
if (!$this->_FieldExists('products_unit', TABLE_ORDERS_INVOICES_PRODUCTS))
{
    $db->Execute("ALTER TABLE " . TABLE_ORDERS_INVOICES_PRODUCTS . " ADD COLUMN products_unit INT(11) NULL DEFAULT 0 ;");
}
// fix wrong html comments in pdf templates
$sql = "UPDATE ".TABLE_PDF_MANAGER_CONTENT." SET template_body = REPLACE(template_body,'-->', ' -->')";//.right { float: right;}
$db->Execute($sql);
// remove right float class in pdf templates
$sql = "UPDATE ".TABLE_PDF_MANAGER_CONTENT." SET template_body = REPLACE(template_body,'.right { float: right;}', '.right-REMOVED_BY_510_UPDATER { float: right;}')";
$db->Execute($sql);