<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

global $db;

$db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES_TEMPLATES."` CHANGE COLUMN `template_name` `template_name` varchar(255) NOT NULL DEFAULT '';");
$db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES_TEMPLATES."` CHANGE COLUMN `template_type` `template_type` varchar(64) NOT NULL DEFAULT '';");
$db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES_TEMPLATES."` CHANGE COLUMN `template_pdf_out_name` `template_pdf_out_name` varchar(512) NOT NULL DEFAULT '';");

$db->Execute("ALTER TABLE `".TABLE_ORDERS_INVOICES_TEMPLATES_CONTENT."` CHANGE COLUMN `template_body` `template_body` text NULL;");
