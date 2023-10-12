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

// TOD entweder alles in controller oder hierher, besser zum controller
class order_edit {

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
        return false;
    }

    public static function get($id)
    {
        return false;
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
        global $db, $xtPlugin;

        $sql = "SELECT `customers_status` FROM ". TABLE_ORDERS . " WHERE `orders_id`=?";
        $cStatusId = $db->GetOne($sql, array($orderId));
        $sql = "SELECT `customers_status_name` FROM ". TABLE_CUSTOMERS_STATUS_DESCRIPTION . " WHERE `customers_status_id`=? AND `language_code`=?";
        $cGroupName = $db->GetOne($sql, array($cStatusId, _STORE_LANGUAGE));
        $sql = "SELECT `customers_status_show_price_tax` FROM ". TABLE_CUSTOMERS_STATUS . " WHERE `customers_status_id`=?";
        $showGrossPrice = $db->GetOne($sql, array($cStatusId));
        $netGross = $showGrossPrice ? __text('TEXT_TAX_INC') : __text('TEXT_TAX_EXC');
        $infoText = ' ('.__text('TEXT_ORDER').' '.$orderId.', '.$cGroupName.' - '.__text('TEXT_PRODUCT_PRICE').': '.$netGross.')';

        define("TEXT_ORDER_EDIT_ITEMS_".$orderId , __text('TEXT_ORDER_EDIT_ITEMS').$infoText);
        define("TEXT_ORDER_EDIT_ADD_ITEM_".$orderId , __text('TEXT_ORDER_EDIT_ADD_ITEM').$infoText);

        // edit window
        $extF_edit = new ExtFunctions();
        $extF_edit->setCode('order_edit_items');
        $remoteWindow = $extF_edit->_RemoteWindow("TEXT_ORDER_EDIT_ITEMS_".$orderId,"TEXT_ORDER","adminHandler.php?plugin=order_edit&load_section=order_edit_products&pg=overview&orders_id='+edit_id+'", '', array(), 900, 600, 'window');

        $remoteWindow->setModal(true);
        $js_edit = "var edit_id = ".$orderId.";";
        $js_edit.= $remoteWindow->getJavascript(false, "new_window").' new_window.show();';

        // add window
        $extF_add = new ExtFunctions();
        $code = 'order_edit_add_items';
        $extF_add->setCode($code);
        $remoteWindow = $extF_add->_RemoteWindow("TEXT_ORDER_EDIT_ADD_ITEM_".$orderId,"TEXT_PRODUCTS","adminHandler.php?plugin=order_edit&load_section=order_edit_add_products&pg=overview&orders_id='+edit_id+'", '', array('modal'=>true), 900, 600, 'window');
        $remoteWindow->setModal(true);
        // open add order items window
        $js_add = "var edit_id = ".$orderId.";";
        $js_add.= $remoteWindow->getJavascript(false, "new_window").' new_window.show();';
        $UserButtons[$code] = array('text'=>'TEXT_ORDER_EDIT_ADD_ITEMS'.$infoText, 'style'=>$code, 'icon'=>'basket_add.png', 'acl'=>'edit', 'stm' => $js_add);
        $params['display_'.$code.'Btn'] = true;

        // coupon window
        $couponsActive = order_edit_controller::isCouponPluginActive();
        if ($couponsActive)
        {
            $extF_coupon = new ExtFunctions();
            $code = 'order_edit_edit_coupon';
            $extF_coupon->setCode($code);
            $remoteWindow = $extF_coupon->_RemoteWindow("TEXT_ORDER_EDIT_EDIT_COUPON","TEXT_COUPONS","adminHandler.php?plugin=order_edit&load_section=order_edit_edit_coupon&pg=edit_coupon&edit_id=1&orders_id='+orders_id+'", '', array('modal'=>true), 900, 325, 'window');
            $remoteWindow->setModal(true);
            // open add order items window
            $js_coupon = "var orders_id = ".$orderId.";";
            $js_coupon.= $remoteWindow->getJavascript(false, "new_window").' new_window.show();';
            $UserButtons[$code] = array('text'=>'TEXT_ORDER_EDIT_EDIT_COUPON', 'style'=>$code, 'icon'=>'money_euro.png', 'acl'=>'edit_coupon', 'stm' => $js_coupon);
            $params['display_'.$code.'Btn'] = true;
        }

        // address window
        $extF_editAddress = new ExtFunctions();
        $code = 'order_edit_edit_address';
        $extF_editAddress->setCode($code);
        $remoteWindow = $extF_editAddress->_RemoteWindow("TEXT_ORDER_EDIT_EDIT_ADDRESS","TEXT_ADDRESS","adminHandler.php?plugin=order_edit&load_section=order_edit_edit_address&pg=edit_address&edit_id=1&orders_id='+orders_id+'", '', array('modal'=>true), 720, 725, 'window');
        $remoteWindow->setModal(true);
        // open add order items window
        $js_address = "var orders_id = ".$orderId.";";
        $js_address.= $remoteWindow->getJavascript(false, "new_window").' new_window.show();';
        $UserButtons[$code] = array('text'=>'TEXT_ORDER_EDIT_EDIT_ADDRESS', 'style'=>$code, 'icon'=>'vcard_edit.png', 'acl'=>'edit_address', 'stm' => $js_address);
        $params['display_'.$code.'Btn'] = true;


        // payment/shipping window
        $extF_editPaymentShipping = new ExtFunctions();
        $code = 'order_edit_edit_paymentShipping';
        $extF_editPaymentShipping->setCode($code);
        $remoteWindow = $extF_editPaymentShipping->_RemoteWindow("TEXT_ORDER_EDIT_EDIT_PAYMENT_SHIPPING","TEXT_EDIT","adminHandler.php?plugin=order_edit&load_section=order_edit_edit_paymentShipping&pg=edit_address&edit_id=1&orders_id='+orders_id+'", '', array('modal'=>true), 700, 305, 'window');
        $remoteWindow->setModal(true);
        // open add order items window
        $js_paymentShipping = "var orders_id = ".$orderId.";";
        $js_paymentShipping.= $remoteWindow->getJavascript(false, "new_window").' new_window.show();';
        $UserButtons[$code] = array('text'=>'TEXT_SAVE', 'style'=>$code, 'icon'=>'folder_wrench.png', 'acl'=>'edit_address', 'stm' => $js_paymentShipping);
        $params['display_'.$code.'Btn'] = true;

        // panel and buttons
        $pnl = new PhpExt_Panel();
        $next_butn_pos = 1;
        $pnl->getTopToolbar()->addIconButton($next_butn_pos++, __text('TEXT_ORDER_EDIT_ITEMS'), 'fas fa-shopping-cart', new PhpExt_Handler(PhpExt_Javascript::stm($js_edit)));
        $pnl->getTopToolbar()->addIconButton($next_butn_pos++, __text('TEXT_ORDER_EDIT_ADD_ITEM'), 'fas fa-cart-plus', new PhpExt_Handler(PhpExt_Javascript::stm($js_add)));
        if ($couponsActive)
        {
            $pnl->getTopToolbar()->addIconButton($next_butn_pos++, __text('TEXT_ORDER_EDIT_EDIT_COUPON'), 'fas fa-percent', new PhpExt_Handler(PhpExt_Javascript::stm($js_coupon)));
        }
        $pnl->getTopToolbar()->addIconButton($next_butn_pos++, __text('TEXT_ORDER_EDIT_EDIT_ADDRESS'), 'fas fa-address-card', new PhpExt_Handler(PhpExt_Javascript::stm($js_address)));
        $pnl->getTopToolbar()->addIconButton($next_butn_pos++, __text('TEXT_ORDER_EDIT_EDIT_PAYMENT_SHIPPING'), 'fas fa-shipping-fast', new PhpExt_Handler(PhpExt_Javascript::stm($js_paymentShipping)));
        $pnl->setRenderTo(PhpExt_Javascript::variable("Ext.get('oe-menubar'+".$orderId.")"));
        ($plugin_code = $xtPlugin->PluginCode('class.order_edit.php:hook_order_edit_display_tpl:add_menu_button_at_next_btn_pos')) ? eval($plugin_code) : false;

        /*
        $js .= PhpExt_Ext::onReady(
            '$("#memoContainer"+'.$orderId.').parent().prepend("<div class=\'order_menubar\' id=\'order-menubar'.$orderId.'\'></div>");',
            '$("#memoContainer"+'.$orderId.').parent().find(\'#order-menubar'.$orderId.'\').prepend("<div id=\'oe-menubar'.$orderId.'\'></div>");',
            $pnl->getJavascript(false, "oeMenubar")
        );
*/
        switch ($_REQUEST['openRemoteWindow']) {
            case 'addProducts':
                $js .= PhpExt_Ext::onReady(
                    $js_add
                );
                break;
            default:
        }
        return $pnl;

    }
}
