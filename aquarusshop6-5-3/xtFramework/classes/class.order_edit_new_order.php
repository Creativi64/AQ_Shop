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

class order_edit_new_order {

    public const XT_ORDER_EDIT_LOGIN_TIME_FRAME_1 = 5;
    public const XT_ORDER_EDIT_LOGIN_TIME_FRAME_2 = 60;

    private $_master_key = 'id';

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        $header['id'] = array('type' => 'hidden', 'readonly'=>true);
        $header['products_id'] = array('type' => 'hidden', 'readonly'=>true);
        $header['products_key'] = array('type' => 'textfield');
        $header['stock'] = array('type' => 'textfield');

        $params = array();
        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = false;
        $params['display_resetBtn'] = false;
        $params['display_editBtn'] = false;
        $params['display_newBtn'] = false;
        $params['display_searchPanel']  = false;

        return $params;
    }

    function _get($ID = 0)
    {
        $data = array();

        $obj = new stdClass;
        $obj->totalCount = 0;
        $obj->data = $data;
        return $obj;
    }

    function _set($data, $set_type = 'edit')
    {
        return false;
    }

    function _unset($id = 0)
    {
        return false;
    }

    public static function hook_order_edit_display_tpl($orderId, &$js)
    {
        define("TEXT_ORDER_EDIT_ITEMS_"+$orderId, __text('TEXT_ORDER_EDIT_ITEMS').' ('.__text('TEXT_ORDER').' '.$orderId.')');

        $extF = new ExtFunctions();
        $extF->setCode('order_edit_items');
        $remoteWindow = $extF->_RemoteWindow("TEXT_ORDER_EDIT_ITEMS_"+$orderId,"TEXT_ORDER","adminHandler.php?plugin=order_edit&load_section=order_edit_products&pg=overview&orders_id='+edit_id+'", '', array(), 800, 600, 'window');
        $saveBtn = PhpExt_Button::createTextButton(__text('BUTTON_SAVE'),new PhpExt_Handler(PhpExt_Javascript::stm("contentTabs.getActiveTab().getUpdater().refresh();")));
        $remoteWindow->addButton($saveBtn);
        $remoteWindow->setModal(true);
        // 'schliessen' gegen 'fertig' ersetzen
        //$btns = &($remoteWindow->getButtons());
        $closeBtn = PhpExt_Button::createTextButton(__text('BUTTON_DONE'),new PhpExt_Handler(PhpExt_Javascript::stm("if (new_window) { new_window.destroy() } else{ this.destroy() } ")));
        $closeBtn->setIcon("images/icons/cancel.png")
            ->setIconCssClass("x-btn-text");
        $remoteWindow->addButton($closeBtn);

        $jsX = "var edit_id = ".$orderId.";";
        $jsX.= $remoteWindow->getJavascript(false, "new_window").' new_window.show();';

        $pnl = new PhpExt_Panel();
        $pnl->getTopToolbar()->addButton(1, __text('TEXT_ORDER_EDIT_ITEMS'), 'images/icons/basket_edit.png', new PhpExt_Handler(PhpExt_Javascript::stm($jsX)));
        $pnl->setRenderTo(PhpExt_Javascript::variable("Ext.get('oe-menubar'+".$orderId.")"));

        $js .= PhpExt_Ext::onReady(
            '$("#memoContainer"+'.$orderId.').parent().prepend("<div id=\'oe-menubar'.$orderId.'\'></div>");',
            $pnl->getJavascript(false, "oeMenubar")
        );
    }

    public function openNewOrderWindowFrontend($data)
    {
        global $db, $xtLink;

        unset($_SESSION['registered_customer']);
        unset($_SESSION['customer']);
        unset($_SESSION['cart']);

        $adminUser = $_SESSION['admin_user'];
        if ($adminUser && $adminUser['user_id'] && $data['customers_id'] && $data['customers_email'])
        {
            $cid = intval($data['customers_id']);
            $sql = "select s.* from ".TABLE_MANDANT_CONFIG." s, ".TABLE_CUSTOMERS." c
                    where c.customers_id = ? and c.shop_id = s.shop_id";
            $record = $db->Execute($sql, array($cid));
            if ($record->_numOfRows==1)
            {
                $payload = array(
                    'adminUser' => $adminUser,
                    'userEmail' => $data['customers_email'],
                    'userId' => $data['customers_id'],
                    'time' => time()
                );

                $sr = order_edit_tools::createSignedRequest($payload, _SYSTEM_SECURITY_KEY);

                // set the right urls in  xtlink
                $query = "SELECT * FROM " . TABLE_MANDANT_CONFIG . " where shop_id =?";
                $store_config = $db->GetArray($query, array($record->fields['shop_id']));
                if (count($store_config) == 1) {
                    $export_store_config = $store_config[0];

                    $xtLink->setLinkURL('http://'.$export_store_config['shop_ssl_domain']);
                    $xtLink->setSecureLinkURL('https://'.$export_store_config['shop_ssl_domain']);
                }

                $url = $xtLink->_link(
                    array(
                        'page' => 'customer',
                        'paction' => 'login',
                        'conn' => $record->fields['shop_ssl'] != 0 ? 'SSL' : 'NOSSL',
                        'params' => "sr=$sr",
                    ),
                    '/xtAdmin', true
                );
                $xtLink->_redirect($url);
            }
            die();
        }
    }

    public function openNewOrderTabBackend($data)
    {
        global $xtLink, $db;

        unset($_SESSION['registered_customer']);
        unset($_SESSION['customer']);
        unset($_SESSION['cart']);

        $order = new order(0, $data['customers_id']);

        $customer = new customer($data['customers_id']);
        
        if (!$customer->customer_default_address || !$customer->customer_shipping_address || !$customer->customer_payment_address) {
        	die('Please add valid payment/shipping address for this customer.');
        }

        $sql = "SELECT `config_value` FROM ". TABLE_CONFIGURATION_MULTI.$customer->customer_info['shop_id'] . " WHERE `config_key`='_STORE_CURRENCY' ";
        $cur = $db->GetOne($sql);

        $cart = new cart();
        $_SESSION['cart'] = $cart;

        $orderData = array(
            'payment_code' => _SYSTEM_ORDER_EDIT_NEW_ORDER_PAYMENT, //$order->order_data['payment_code'],
            'subpayment_code' => '', //$order->order_data['subpayment_code'],
            'shipping_code' => _SYSTEM_ORDER_EDIT_NEW_ORDER_SHIPPING,  //$order->order_data['shipping_code'],
            'currency_code' => $cur,  //$order->order_data['currency_code'],
            'currency_value' => '1',  //$order->order_data['currency_value'],
            'orders_status' => '',  //$order->order_data['orders_status'],
            'account_type' => $customer->account_type,  //$order->order_data['account_type'],
            'allow_tax' => '',  //$order->order_data['allow_tax'],
            'comments' => '', //$order->order_data['comments'],
            'customers_id' => $data['customers_id'],
            'source_id' => _SYSTEM_ORDER_EDIT_NEW_ORDER_ORDER_SOURCE,
            'shop_id' => $customer->customer_info['shop_id'], //$order->order_data['shop_id'],
            'customers_ip' => '', //$order->order_data['customers_ip'],
            'delivery' => $customer->customer_shipping_address, //$this->_customer->customer_shipping_address,
            'billing' => $customer->customer_payment_address
        );
        $savedOrder = $order->_setOrder($orderData,'complete','insert', '');

        if (!$savedOrder['orders_id'])
        {
            $msg = '<div class="order_edit_error_box">Could not create order.<br />';
            if (!$customer->customer_default_address) $msg .= '<br/> Customers default address is empty.';
            if (!$customer->customer_shipping_address) $msg .= '<br/> Customers shipping address is empty.';
            if (!$customer->customer_payment_address) $msg .= '<br/> Customers payment address is empty.';
            $msg .= '</div>';
            die($msg);
        }

        $remoteWindow = '';
        if ($data['openRemoteWindow'])
        {
            $remoteWindow = '&openRemoteWindow='.$data['openRemoteWindow'];
        }
        // redirect zur order_edit.php
        $xtLink->_redirect('order_edit.php?pg=overview&parentNode=node_order&gridHandle=ordergridForm&edit_id='.$savedOrder['orders_id'].$remoteWindow);
    }
}