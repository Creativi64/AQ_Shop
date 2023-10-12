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

global $info, $_content, $page, $store_handler, $brotkrumen, $xtLink, $xtPlugin;

$show_index_boxes = false;

if ($page->page_action == 'payment' &&
    (!empty($_SESSION['selected_payment_discount']) || !empty($_SESSION['selected_payment']))
)
{
    unset($_SESSION['selected_payment']);
    unset($_SESSION['selected_payment_discount']);
    if(is_object(sessionCart())) sessionCart()->_refresh();
}

$checkout = new checkout();

($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_first')) ? eval($plugin_code) : false;

if(!$_SESSION['registered_customer']) {
	$tmp_link  = $xtLink->_link(array('page'=>'checkout','paction'=>'shipping','conn'=>'SSL'));
	$brotkrumen->_setSnapshot($tmp_link);
	$xtLink->_redirect($xtLink->_link(array('page'=>'customer', 'paction'=>'login','conn'=>'SSL')));
}

// redirect if customer is not allowed to see prices
if (_CUST_STATUS_SHOW_PRICE=='0')
    $xtLink->_redirect($xtLink->_link(array('page'=>'cart')));

if(!empty($checkout_id)){
	$table_id = $checkout_id;
}else{
	$table_id = $store_handler->shop_id;
}


// Add Payment and Shipping Classes:

if(isset($_POST['action']))
{
    $checkout->process_pageAction($_POST['action']);
}


/**
 * payment processing part (checkout -> redirect to payment -> return to payment_process)
 */
if($page->page_action=='payment_process'){
     include _SRV_WEBROOT._SRV_WEB_CORE.'pages/page_action/checkout.payment_process.php';   
}

/**
 * payment module is for external PSP with POST form require
 */
if ($page->page_action=='pay') {
    include _SRV_WEBROOT._SRV_WEB_CORE.'/pages/page_action/checkout.pay.php';
}

/**
 *
 *
 * BUILD PAGE (DISPLAY)
 *
 *
 */

$shipping_address = $_SESSION['customer']->customer_shipping_address;
$payment_address = $_SESSION['customer']->customer_payment_address;

$shipping_data = array();
$payment_data = array();

// solang wir keine one-page-checkout haben brauchen wir nie alles
// ausser zb wir sind in pp express, macht das pp plugin ab version 2.9.0 für xt > 6.1.1

$shipping_data = $checkout->_selectShipping();
$payment_data = $checkout->_selectPayment();

switch($page->page_action)
{
    case 'shipping':
        $addr_filter = array('default', 'shipping', 'payment');
        $shipping_to = '';
        if(count($checkout->shipping_to['countries']))
        {
            $shipping_to = implode(', ', $checkout->shipping_to['countries']);
        }
        if(count($checkout->shipping_to['zones']))
        {
            if(count($checkout->shipping_to['countries']))
                $shipping_to .= ', ';
            $shipping_to .= implode(', ', $checkout->shipping_to['zones']);
        }
        if(!empty($shipping_to))
        {
            $shipping_to = sprintf(constant('TEXT_POSSIBLE_SHIPPING_COUNTRIES_ZONES'), $shipping_to);
            global $system_shipping_link;
            if(empty($system_shipping_link))
            {
                $system_shipping_link = new system_shipping_link();
            }
            $shipping_link = $system_shipping_link->shipping_link;
            if(!empty($shipping_link))
            {
                $shipping_to .= sprintf(' <a href="%s" target="_blank" style="color:inherit; text-decoration: underline">'.constant('TEXT_SHIPPING_INFOS').'</a>', $shipping_link);
            }
            $info->_addInfo($shipping_to, 'info');
        }
        break;
    case 'payment':
        $addr_filter = array('default', 'payment', 'shipping');

        if(empty($payment_data))
        {
            $no_payment_msg = constant('TEXT_NO_PAYMENT_METHOD_FOR_CART');

            $content_array_contact = $_content->getHookContent(5);

            if(!empty($content_array_contact))
            {
                $no_payment_msg .= sprintf(' <a href="%s" target="_blank" style="color:inherit; text-decoration: underline">'.constant('TEXT_CONTACT_PAGE').'</a>', $content_array_contact["content_link"]);
            }
            $info->_addInfo($no_payment_msg, 'error');
        }

        break;
    default:
        $addr_filter = false;
}

($plugin_code = $xtPlugin->PluginCode('module_checkout.php:before_getAdressList')) ? eval($plugin_code): false;

$address_data = sessionCustomer()->_getAdressList($_SESSION['registered_customer'], $addr_filter);
if(empty($address_data)) $address_data = [];
$address_count = is_countable($address_data) ? count($address_data) : 0;
$address_max_count = (int)_STORE_ADDRESS_BOOK_ENTRIES;

$add_address = 0;
if($address_count < $address_max_count)
$add_address = 1;

$tmp_address_array[] = array('id'=>'', 'text'=>TEXT_SELECT);
$address_data =  array_merge($tmp_address_array,$address_data);

$page_data = $page->page_action;

if($_SESSION['cart']->type == 'virtual'){
	if($page_data=='shipping'){
		$xtLink->_redirect($xtLink->_link(array('page'=>'checkout', 'paction'=>'payment','conn'=>'SSL')));	
	}
}

($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_pages')) ? eval($plugin_code) : false;

/**
 * show shipping page
 */
if ($page_data=='shipping') {
    include _SRV_WEBROOT._SRV_WEB_CORE.'pages/page_action/checkout.shipping.php';
}


($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_selections')) ? eval($plugin_code) : false;

$checkout_data  = array('page_action'=>$page_data,
							'address_data' => $address_data,
							'add_new_address' => $add_address,
							'shipping_address' => $shipping_address,
							'payment_address' => $payment_address,
							'shipping_data' => $shipping_data,
							'payment_data' => $payment_data
);

/**
 * payment module is for external PSP with POST form require
 */
if ($page->page_action=='pay') {
    include _SRV_WEBROOT._SRV_WEB_CORE.'/pages/page_action/checkout.pay.php';
}

/**
 * show payment page
 */
if($page->page_action=='payment'){
    include _SRV_WEBROOT._SRV_WEB_CORE.'/pages/page_action/checkout.payment.php'; 
}

/**
 * show confirmation page
 */
if($page->page_action=='confirmation'){
    include _SRV_WEBROOT._SRV_WEB_CORE.'/pages/page_action/checkout.confirmation.php';
}

/**
 * show success page
 */
if($page->page_action=='success'){
    include _SRV_WEBROOT._SRV_WEB_CORE.'pages/page_action/checkout.success.php';
    //reset $info on checkout sites
    $info->_showInfo('store');
}

($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_page_actions')) ? eval($plugin_code) : false;      

if($_GET['error']) {
	$param ='/[^a-zA-Z0-9_-]/';
	$field=preg_replace($param,'',$_GET['error']);
	if (defined($field) &&
        (strpos( $field, 'ERROR_' ) === 0 || strpos( $field, 'TEXT_' ) === 0) )
	$info->_addInfo(constant($field));
}

$tpl_data = array('message'=>$info->info_content);
$tpl_data = array_merge($tpl_data, $checkout_data);
($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_data')) ? eval($plugin_code) : false;
if (isset($_SESSION['cart']->discount))
{
    if ($_SESSION['cart']->discount != 'false')
	{
        if (is_array($data)) {  // TODO was ist das, wo wird data initialisiert, per hook in einem plg?
            $data = array_merge($data,array('discount'=>$_SESSION['cart']->discount));
        } else {
            $data = array('discount'=>$_SESSION['cart']->discount);
        }
		$tpl_data = array_merge($tpl_data, $data);
    }
}

$template = new Template();
$tpl = '/'._SRV_WEB_CORE.'pages/checkout.html';
($plugin_code = $xtPlugin->PluginCode('module_checkout.php:checkout_bottom')) ? eval($plugin_code) : false;
$page_data = $template->getTemplate('smarty', $tpl, $tpl_data);
