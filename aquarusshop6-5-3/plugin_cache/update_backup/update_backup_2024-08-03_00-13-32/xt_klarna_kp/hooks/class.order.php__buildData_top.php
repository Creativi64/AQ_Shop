<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $kp_order, $db;

if($_REQUEST['gridHandle'] == 'ordergridForm' &&
    strpos($_REQUEST['parentNode'], 'node_order') === 0 &&
    $_REQUEST['pg'] == 'overview')
{
    $klarna_order_id = xt_klarna_kp::getKlarnaOrderId($oID);

    if($klarna_order_id)
    {
        try
        {
        $kp = klarna_kp::getInstance()->setXtOrderId($oID);
        $kp_order = $kp->getOrder($klarna_order_id);
        klarna_kp::setKlarnaOrderInXt($kp_order, $oID);

            /**
             *   setzen des über klarna gemapten status machen wir doch nur bei anlage der order
             *   sonst wird hier immer der klarna-status gesetzt statt des evtl manuell eingegebenen
             */
            /*
        // status mapping läuft über fraud_staus, ausser stats == canceled, dann eben über canceled
        $store_id = $db->GetOne("SELECT shop_id FROM ".TABLE_ORDERS." WHERE orders_id=?", array($oID));
        $orders_status_id = $db->GetOne("SELECT orders_status FROM ".TABLE_ORDERS." WHERE orders_id=?", array($oID));

        $kp_order_status = $kp_order['status'] == 'CANCELLED' ? 'CANCELED' : $kp_order['fraud_status'];
        $mapped_xt_status = xt_klarna_kp::getMappedXtOrderStatus($kp_order_status, $store_id);
        if($mapped_xt_status && (int)$mapped_xt_status != (int)$orders_status_id)
        {
            $order2update = new order();
            $order2update->oID = $oID;
            $order2update->_updateOrderStatus($mapped_xt_status,'', false, false, 'klarna status mapping', 0, '');
        }
            */

        $kp_order = $kp_order->getArrayCopy();
        }
        catch(Exception $ex)
        {
            echo '<script> alert("Klarna KP: '.strtok( $ex->getMessage(), "\n").' see xtLogs/klarna_kp.log");</script>';
            klarna_kp::log('open order in backend', ['orders_id' => $oID],$kp_order, $ex );
        }
    }
}