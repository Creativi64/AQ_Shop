<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_bank_details/classes/constants.php';

global $db, $xtPlugin;

$sql = "SELECT bd.* FROM ".TABLE_BAD_BANK_DETAILS. " bd
				LEFT JOIN ".TABLE_BAD_ORDER_BANK_DETAILS." obd ON bd.".COL_BAD_BANK_DETAILS_ID_PK."=obd.".COL_BAD_BANK_DETAILS_ID."
				LEFT JOIN ".TABLE_ORDERS." o ON o.orders_id=obd.".COL_BAD_ORDERS_ID."
				WHERE o.orders_id=?";
/** @var  $oID int|string */
$bd = $db->Execute($sql, array($oID));

if($bd->RowCount()>0)
{
    while(!$bd->EOF)
    {
        $bank_details = $bd->fields;
        // def order data
        $order_data['order_data']['bank_details'][] = $bank_details;

        // order_info_option data in order.html
        $issuer = defined('TEXT_'.strtoupper($bd->fields[COL_BAD_TECH_ISSUER])) ? constant('TEXT_'.strtoupper($bd->fields[COL_BAD_TECH_ISSUER])) : $bd->fields[COL_BAD_TECH_ISSUER];
        $order_data['order_data']['order_info_options'][] = array('text'=>'<span class="bold" style="font-weight:bold">'.TEXT_BANK_DETAILS.' '.$issuer.'</span>', 'value'=>'');
        foreach($bd->fields as $k => $v)
        {
            if ($k==COL_BAD_BANK_DETAILS_ID_PK || $k==COL_BAD_TECH_ISSUER || $k==COL_BAD_ADD_DATA) continue;
            if(empty($v)) continue;
            $order_data['order_data']['order_info_options'][] = array(
                'text'=> '&nbsp;<span class="" style="">'.constant('TEXT_'.strtoupper($k)).'</span>',
                'value'=> $v);
        }
        ($plugin_code = $xtPlugin->PluginCode('bank_details_hook::class.order.php:_buildData_bottom')) ? eval($plugin_code) : false;
        $bd->MoveNext();
    }
}
