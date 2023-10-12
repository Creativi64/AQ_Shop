<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');
global $info, $xtLink;

if (isset($_REQUEST['ppp_save_comment']))
{
    $result = array('result' => 'OK - not saved');
    if (is_data($_POST['comments']))
    {
        $_SESSION['order_comments'] = $_POST['comments'];

        $result = array('result' => 'OK - saved');
    }
    header("HTTP/1.0 200 OK");
    echo json_encode($result);
    die();
}

include_once _SRV_WEBROOT . 'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
$ppp = new paypal_plus;

if (isset($_GET['ppp_action']))
{
    $action = $_GET['ppp_action'];
}
else
{
    $action = 'confirmation';
}

if ($_GET['success'] == 'true')
{
    if ($_SESSION['plus_' . $ppp->customer]['PPPLUS_PAYMENT_ID'] == $_GET['paymentId'])
    {
        $_SESSION['selected_payment'] = 'xt_paypal_plus';
        $ppp->getPaymentDetails($_GET['PayerID']);
    }
    else
    {
        $info->_addInfo(TEXT_PAYPAL_PLUS_PAYMENT_MISSING_CREDENTIALS, 'error');
        $tmp_link = $xtLink->_link(array('page' => 'checkout', 'paction' => 'payment', 'conn' => 'SSL'));
        $xtLink->_redirect($tmp_link);
    }
}
else
{
    if (isset($_GET['ppp_payment']))
    {
        //$_SESSION['selected_payment'] = $_GET['ppp_payment'];
        $checkout = new checkout();
        $checkout->_setPayment($_GET['ppp_payment']);
        $tmp_payment_data = $checkout->_getPayment();
        $payment_data = $tmp_payment_data[$_GET['ppp_payment']];
        if ($payment_data['payment_price']['discount'] != 1)
        {
            $payment_data_array = array('customer_id' => $_SESSION['registered_customer'],
                'qty' => $payment_data['payment_qty'],
                'name' => $payment_data['payment_name'],
                'model' => $payment_data['payment_code'],
                'key_id' => $payment_data['payment_id'],
                'price' => $payment_data['payment_price']['plain_otax'],
                'tax_class' => $payment_data['payment_tax_class'],
                'sort_order' => $payment_data['payment_sort_order'],
                'type' => $payment_data['payment_type']
            );
            $_SESSION['cart']->_deleteSubContent('payment');
            $_SESSION['cart']->_addSubContent($payment_data_array);
            $_SESSION['cart']->_refresh();
        }
        if ($payment_data['payment_price']['plain_otax'] <= 0)
        {
            $_SESSION['cart']->_deleteSubContent('payment');
            $_SESSION['cart']->_refresh();
        }
    }
    else
    {
        $info->_addInfoSession(TEXT_PPP_PAYMENT_CANCELED_BY_USER, 'info');
        $tmp_link = $xtLink->_link(array('page' => 'checkout', 'paction' => 'payment', 'conn' => 'SSL'));
        $xtLink->_redirect($tmp_link);
    }
}

$tmp_link = $xtLink->_link(array('page' => 'checkout', 'paction' => 'confirmation', 'conn' => 'SSL'));
$xtLink->_redirect($tmp_link);
