<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("UPDATE " . TABLE_ADMIN_NAVIGATION . " SET `type` = 'G' WHERE `text` = 'order'");

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('paypal_transactions','paypal_refunds')");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_transactions', 'images/icons/money_euro.png', '&plugin=xt_paypal', 'adminHandler.php', '4000', 'order', 'I', 'W');");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_refunds', 'images/icons/money_euro.png', '&plugin=xt_paypal', 'adminHandler.php', '4000', 'order', 'I', 'W');");