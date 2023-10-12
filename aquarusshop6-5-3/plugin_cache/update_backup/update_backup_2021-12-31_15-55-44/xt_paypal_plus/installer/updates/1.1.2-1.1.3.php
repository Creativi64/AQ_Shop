<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');


// fix wrong translation for XT_PAYPAL_PLUS_ORDER_STATUS_EXPIRED_TITLE
global $db;
$langs = array(
    'de' => 'Bestellstatus  - Abgelaufen',
    'en' => 'Ordersstatus - Expired',
    'es' => 'Estado del pedido - caducado',
    'bp' => 'Status dos pedidos - expirado');

// if there are other languages the provided by installer set default en
$sql = "UPDATE ".TABLE_LANGUAGE_CONTENT. " SET `language_value`=? WHERE `language_key`='XT_PAYPAL_PLUS_ORDER_STATUS_EXPIRED_TITLE' AND `language_code`=?";
$db->Execute($sql, array($langs['en'], 'en'));

foreach($langs as $lang => $content)
{
    $sql = "UPDATE ".TABLE_LANGUAGE_CONTENT. " SET `language_value`=? WHERE `language_key`='XT_PAYPAL_PLUS_ORDER_STATUS_EXPIRED_TITLE' AND `language_code`=?";
    $db->Execute($sql, array($content, $lang));
}