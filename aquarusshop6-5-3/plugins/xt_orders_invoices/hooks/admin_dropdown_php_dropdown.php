<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_orders_invoices/classes/constants.php';

if ($request['get'] == 'xt_orders_invoices') {
    $result = array();
    $result[] = array('id' => 'order', 'name' => XT_ORDERS_INVOICES_ANCHOR_ORDER, 'desc' => XT_ORDERS_INVOICES_ANCHOR_ORDER);
    $result[] = array('id' => 'shipment', 'name' => XT_ORDERS_INVOICES_ANCHOR_SHIPMENT, 'desc' => XT_ORDERS_INVOICES_ANCHOR_SHIPMENT);
}

if ($request['get'] == 'template_type') {
    $result = array();
    global $db;
    $sql = "SELECT DISTINCT `".COL_TEMPLATE_TYPE."` FROM ".TABLE_ORDERS_INVOICES_TEMPLATES;
    $rs = $db->Execute($sql);
    while(!$rs->EOF) {
        $result[] = array('id' => $rs->fields[COL_TEMPLATE_TYPE], 'name' => $rs->fields[COL_TEMPLATE_TYPE], 'desc' => $rs->fields[COL_TEMPLATE_TYPE]);
        $rs->MoveNext();
    }

    ($plugin_code = $xtPlugin->PluginCode('dropdown_data:template_type')) ? eval($plugin_code) : false;
}


if ($request['get'] == 'profile_e_invoice') {
    $result = array();
    $result[] = array('id' => '0', 'name' => '- none -', 'desc' => '');
    $result[] = array('id' => '1', 'name' => 'PROFILE_MINIMUM', 'desc' => '');
    $result[] = array('id' => '2', 'name' => 'PROFILE_BASIC', 'desc' => '');
    $result[] = array('id' => '3', 'name' => 'PROFILE_EN16931', 'desc' => '');
    $result[] = array('id' => '4', 'name' => 'PROFILE_XRECHNUNG', 'desc' => '');
}
