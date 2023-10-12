<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("UPDATE `".TABLE_ADMIN_NAVIGATION."` SET `parent`='order' WHERE `text`='paypal_plus_transactions' ");
$db->Execute("UPDATE `".TABLE_ADMIN_NAVIGATION."` SET `parent`='order' WHERE `text`='paypal_plus_refunds' ");

$db->Execute("UPDATE `".TABLE_PLUGIN_CONFIGURATION."` SET `config_value`=0 WHERE `config_key`='PAYPAL_PLUS_API_LOGGING' ");

$output .= "
<div style='padding:10px; border:1px solid #005da2; background: #AFD0E1'>Aktivieren Sie nun Plugin und die Zahlungsweise und laden das Backend neu.<br /> Activate plugin and payment method and reload backend.</div>
";