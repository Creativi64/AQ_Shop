<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

global $store_handler, $xtLink, $customers_status, $info;

if(($_SESSION['kp_authorization_token'] || $_REQUEST['kp_authorization_token'])
    && is_array($_SESSION['kp_session']))
{
    try
    {
        $auth_token = $_REQUEST['kp_authorization_token'] ? $_REQUEST['kp_authorization_token'] : $_SESSION['kp_authorization_token'];
        /**
         * create a kp order
         */
        $errors = [];
        $_SESSION['kp_session']['merchant_reference1'] = $_SESSION['last_order_id'];

        $xtkp = new xt_klarna_kp();
        $b2b_groups = $xtkp->get_saved_klarna_kp_b2b_groups(['store_id' => $store_handler->shop_id], true);
        $b2b = in_array($customers_status->customers_status_id, $b2b_groups);

        $addData = [
            'auto_capture' => !empty($_SESSION['cart']) && $_SESSION['cart']->type == 'virtual',
            'merchant_reference1' => $_SESSION['last_order_id']
        ];

        $kp_order = klarna_kp::createPaymentsOrderFromSession($addData, $_SESSION['kp_session']['session_id'], $_SESSION['customer'], $auth_token, $b2b, $errors);

        /**
         * fetch klarna order an save in xt db
         */
        $order_management = klarna_kp::getOrder($kp_order['order_id']);

        $test_group = constant('_KLARNA_CONFIG_KP_TESTMODE_CUSTOMER_GROUP');
        if($test_group == $customers_status->customers_status_id)
        {
            $test_group = (int) $customers_status->customers_status_id;
        }

        klarna_kp::setKlarnaOrderInXt($order_management, $order->oID, constant('_KLARNA_CONFIG_KP_TESTMODE') == 1 || $test_group > 0);

        $kp_order_status = $order_management['status'] == 'CANCELLED' ? 'CANCELED' : $order_management['fraud_status'];
        $mapped_xt_status = xt_klarna_kp::getMappedXtOrderStatus($kp_order_status, $store_handler->shop_id);
        if($mapped_xt_status && (int)$mapped_xt_status != (int)$order->order_data['orders_status_id'])
        {
            $order2update = new order();
            $order2update->oID = $order->oID;
            $order2update->_updateOrderStatus($mapped_xt_status,'', false, false, 'klarna status mapping', 0, '');
        }
        if(!empty($_SESSION['cart']) && $_SESSION['cart']->type == 'virtual')
        {
            $_SESSION['kp_auto_capture_xt_order_id'] = $_SESSION['last_order_id'];
        }

        $order->order_data['payment_name'] = 'Klarna';
        if(!empty($kp_order["redirect_url"]))
        {
            $tmp_link = $kp_order["redirect_url"];
        }

        unset($_SESSION['kp_session']);
        unset($_SESSION['kp_session_b2b']);
        unset($_SESSION['kp_session_lng']);
        unset($_SESSION['kp_authorization_token']);
        unset($_SESSION['kp_finalize_required']);
        unset($_SESSION['kp_selected_payment_method_category']);
        unset($_SESSION['kp_session_coupon_code']);
        //unset($_SESSION['kp_auto_capture_xt_order_id']);  // unset on success page
    }
    catch(Exception $ex)
    {
        error_log($ex->getMessage());
        //$tmp_link = $xtLink->_link(array('page' => 'checkout', 'paction' => 'confirmation', 'conn' => 'SSL'));
        //$xtLink->_redirect($tmp_link);
        $info->_addInfoSession($ex->getMessage(), 'error');
    }
}
