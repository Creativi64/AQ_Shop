<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';


class callback_xt_klarna_kp extends callback
{

    var $log_callback = true;


    function process()
    {
        global $filter, $db, $tax, $countries, $xtLink, $store_handler, $customers_status, $xtPlugin;
        global $debug_ip;

        $this->data = array();
        $raw_data = file_get_contents("php://input");
        klarna_kp::log('callback process', array('_REQUEST' => $_REQUEST, 'php_input' => $raw_data), null);
        error_log($raw_data);


        $allowed_events = array('push', 'notification', 'fetch');
        $response = '';
        $location = '';

        try
        {
            $event = isset($_REQUEST['event']) ? $_REQUEST['event'] : false;

            if ($event!==false && in_array($event, $allowed_events))
            {
                switch ($event)
                {
                    /**
                     *   frontend events
                     */
                    case 'fetch':
                        // what ever happens, we give just one try
                        unset($_SESSION['kp_auto_capture_xt_order_id']); // set in display_bottom on success page

                        $xt_order_id = $_REQUEST['xt_order_id'];
                        if(empty($xt_order_id)) throw new Exception('xt order id not found in request');

                        $kp_order_id = klarna_kp::getKlarnaOrderId($xt_order_id);
                        $order_management = klarna_kp::getOrder($kp_order_id);
                        $arr_copy = $order_management->getArrayCopy();

                        if($arr_copy['status'] == 'CAPTURED')
                        {
                            klarna_kp::setKlarnaOrderInXt($order_management, $arr_copy['order_id']);
                        }
                        $header = '200';
                        $response = ['x'=>'y'];
                        break;

                    /**
                     * klarna callback events
                     */
                    case 'notification':

                        $kp_order_id = $_REQUEST['kp_order_id'];
                        if(empty($kp_order_id)) throw new Exception('kp order id not found in request');

                        $kp_order = klarna_kp::getOrder($kp_order_id);
                        klarna_kp::setKlarnaOrderIdInXt($kp_order, $kp_order['merchant_reference1']);

                        $header = '201';
                        break;

                    case 'push':

                        $kp_order_id = $_REQUEST['kp_order_id'];
                        if(empty($kp_order_id)) throw new Exception('kp order id not found in request');

                        /**
                         * save klarnas order id in db
                         */
                        $klarna_order = klarna_kp::getKlarnaOrderFromXt(false, $kp_order_id);
                        if(!is_array($klarna_order))
                        {
                            klarna_kp::cancelOrder($kp_order_id);
                            error_log("kp push | kp order [$kp_order_id] not found in xt. order was canceled");
                            $header = '201';
                        }
                        else
                        {
                            $xt_order_id = $db->GetOne("SELECT orders_id FROM " . TABLE_ORDERS . " WHERE kp_order_id=?", array($kp_order_id));

                            /**
                             * acknowledge order in klarna and set merchant ref1 to xt's order id
                             */
                            klarna_kp::setXtOrderIdInKlarna($kp_order_id, $xt_order_id);
                            klarna_kp::acknowledgeOrder($kp_order_id);
                            /**
                             * fetch klarna order an save in xt db
                             */
                            $order_management = klarna_kp::getOrder($kp_order_id);
                            klarna_kp::setKlarnaOrderInXt($order_management, $xt_order_id);

                            $header = '201';
                        }
                        break;

                    default:
                        throw new Exception('event ['.$event.'] not implemented');
                }

                if(!empty($location))
                {
                    header('Location: /'.$location, false, $header);
                }
                else if(!empty($response))
                {
                    header("HTTP/1.1", false, $header);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                }
                else {
                    header("HTTP/1.1", false, $header);
                }

            }
            else {
                if (empty($event))
                {
                    $msg = 'Event parameter empty';
                }
                else if (!in_array($event, $allowed_events))
                {
                    $msg = 'Event ['.$event.'] not allowed';
                }
                else
                {
                    $msg = 'No event parameter';
                }
                throw new Exception($msg);
            }
        }
        catch (Exception $e)
        {
            $event = $event.' ' ?: ' ';
            klarna_kp::log('callback process error', $_REQUEST, array(), $e);
            error_log("kp callback $event| ".$e->getMessage());
            header("HTTP/1.1 500");
        }

    }

}
