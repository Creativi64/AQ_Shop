<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

global $kp_order, $page, $store_handler;

$ko_do_unset = true;

if($page->page_name == 'checkout' && $_REQUEST['action'] == 'payment')
{

    if (strpos($_REQUEST['selected_payment'],':'))
    {
        $_kp_payments = explode(':',$_REQUEST['selected_payment']);
        if($_kp_payments[0] == 'xt_klarna_kp')
        {
            $ko_do_unset = false;
            $_SESSION['kp_authorization_token'] = $_REQUEST['kp_authorization_token'];
            $_SESSION['kp_finalize_required'] = $_REQUEST['kp_finalize_required'];
            $_SESSION['kp_selected_payment_method_category']
                = $_SESSION['selected_payment_sub'] = $_kp_payments[1];
        }
    }
    if($ko_do_unset)
    {
        unset(
            $_SESSION['kp_authorization_token'],
            $_SESSION['kp_finalize_required'],
            $_SESSION['kp_selected_payment_method_category']
        );
    }
}
else if($page->page_name == 'checkout' && $page->page_action == 'confirmation')
{
    if(strstr($_SESSION['selected_payment'], 'xt_klarna_kp') && is_array($_SESSION['kp_session']))
    {
        foreach($_SESSION["kp_session"]["payment_method_categories"] as $pmc)
        {
            if($pmc['identifier'] == $_SESSION['selected_payment_sub'])
            {
                define('TEXT_PAYMENT_'.strtoupper($_SESSION['selected_payment_sub']), $pmc['name']);
                $ko_do_unset = false;
                break;
            }
        }
        if(!defined('TEXT_PAYMENT_'.strtoupper($_SESSION['selected_payment_sub'])))
        {
            define('TEXT_PAYMENT_'.strtoupper($_SESSION['selected_payment_sub']), $_SESSION['selected_payment_sub']);
        }
    }

}
else if($page->page_name == 'checkout' && $_REQUEST['action'] == 'process')
{
    if(strstr($_SESSION['selected_payment'], 'xt_klarna_kp') && is_array($_SESSION['kp_session']))
    {
        if(array_key_exists('kp_authorization_token', $_REQUEST) && !empty($_REQUEST['kp_authorization_token']))
        {
            $_SESSION['kp_authorization_token'] = $_REQUEST['kp_authorization_token'];
        }
    }
}
else if($page->page_name == 'checkout' && $page->page_action == 'success')
{
    if(!empty($_SESSION['kp_auto_capture_xt_order_id'])) // set in checkout_process_bottom if auto_capture = true
    {
        $kp_order_id = klarna_kp::getKlarnaOrderId($_SESSION['kp_auto_capture_xt_order_id']);

        if ($kp_order_id)
        {
            /**
             * fetch klarna order an save in xt db
             */
            $order_management = klarna_kp::getOrder($kp_order_id);
            $arr_copy = $order_management->getArrayCopy();
            if($arr_copy['status'] == 'CAPTURED')
            {
                klarna_kp::setKlarnaOrderInXt($order_management, $_SESSION['kp_auto_capture_xt_order_id']);
                unset($_SESSION['kp_auto_capture_xt_order_id']);
            }
            // else wird in display_bottom ein ajax fetch gemacht
        }
    }
}
