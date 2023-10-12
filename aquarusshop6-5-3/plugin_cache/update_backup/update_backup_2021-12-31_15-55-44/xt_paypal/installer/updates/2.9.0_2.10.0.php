<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $db;

$db->Execute("DELETE FROM ".TABLE_ADMIN_NAVIGATION." WHERE text in ('paypal_transactions','paypal_refunds')");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_transactions', 'images/icons/money_euro.png', '&plugin=xt_paypal', 'adminHandler.php', '4000', 'order', 'I', 'W');");
$db->Execute("INSERT INTO " . TABLE_ADMIN_NAVIGATION . " (`pid` ,`text` ,`icon` ,`url_i` ,`url_d` ,`sortorder` ,`parent` ,`type` ,`navtype`) VALUES (NULL , 'paypal_refunds', 'images/icons/money_euro.png', '&plugin=xt_paypal', 'adminHandler.php', '4000', 'order', 'I', 'W');");

$db->Execute("UPDATE ".TABLE_CONFIGURATION_PAYMENT. " SET `sort_order` = 12 WHERE `config_key` = 'XT_PAYPAL_EXPRESS_PRODUCT'");

$output .=  "<div style='border:1px solid #009900; background:#BDFFA9;padding:10px;'>";
$output .=  "<p>Bitte prüfen Sie in Ihren Templates, dass in product.html das Formular zum Artikel-Hinzufügen eine eindeutige Id hat. Alt:<br /><br />
<pre>{form type=form name=product action='dynamic' link_params=getParams method=post}</pre><br />NEU:<br /><br />
<pre>{form type=form name=product action='dynamic' link_params=getParams method=post id=\"main_product_form\" }</pre>
";
$output .=  "</div>";

