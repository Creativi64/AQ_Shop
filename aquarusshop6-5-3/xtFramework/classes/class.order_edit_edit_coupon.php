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



class order_edit_edit_coupon extends xt_backend_cls {

	function _getParams() {
		global $language;

		$params = array();
        $header['orders_id'] = array('type'=>'hidden');
        //$header['removed_coupon_code'] = array('type'=>'textfield', 'width' => 500, 'readonly' => true, 'text'=>__text('TEXT_ORDER_EDIT_REMOVED_COUPON_CODE'));
		$header['current_coupon_code'] = array('type'=>'hidden', 'width' => 500, 'text'=>__text('TEXT_ORDER_EDIT_CURRENT_COUPON_CODE'));
        $header['current_coupon_code_show'] = array('type'=>'textfield', 'width' => 500, 'readonly' => true, 'text'=>__text('TEXT_ORDER_EDIT_CURRENT_COUPON_CODE'));
        $header['new_coupon_code_coupons'] = array('type'=>'dropdown', 'width' => 500, 'url'  => 'DropdownData.php?get=order_edit_coupons','text'=>__text('TEXT_ORDER_EDIT_NEW_COUPON_CODE_TEMPLATE'));
        $header['new_coupon_code_code'] = array('type'=>'textfield', 'width' => 500, 'text'=>__text('TEXT_ORDER_EDIT_NEW_COUPON_CODE_CODE'));


        $params['header']         = $header;
        $params['master_key']     = 'coupon_code';

        $params['display_resetBtn'] = false;
        $params['display_editBtn'] = false;

        // coupon code speichern
        $js = "
            var cmpId = order_edit_edit_couponbd.id;
            var mask = null;
            var orders_id = ". $this->url_data['orders_id'] . ";
            var form = Ext.getCmp(cmpId).getForm();
            if (form)
            {
                mask = new Ext.LoadMask(form.el, {msg:'Moment'});
                mask.show();

                Ext.getCmp(cmpId).getForm().submit({     
                    url: 'adminHandler.php',
                    method:'POST',
                params: {
                    pg:     'editCoupon',
                    plugin: 'order_edit',
                    load_section:   'order_edit_edit_coupon',
                    orders_id:      orders_id,      
                    mode: 'EDIT_COUPON',
                    sec: '".$_SESSION['admin_user']['admin_key']."'
                },
                success: function(form, action)
                {
                    mask.hide();
                    var r = action.result;
                    if (r.success!=true)
                    {
                        Ext.MessageBox.alert('Error', r.msg);
                        return;
                    }
                    else {
                        contentTabs.getActiveTab().getUpdater().refresh();
                        order_edit_edit_coupongridEditForm.getForm().load({url:'adminHandler.php?load_section=order_edit_edit_coupon&plugin=order_edit&pg=edit_coupon&edit_id=1&orders_id=". $this->url_data['orders_id'] . "&modal=true&parentNode=order_edit_edit_couponRemoteWindow&get_singledata=1&sec=".$_SESSION['admin_user']['admin_key']."', waitMsg:'Loading',method: 'GET'});
                    }
                },
                failure: function(form, action)
                {
                    mask.hide();
                    var title = 'Error ';
                    r = action.result;
                    var msg = r.msg ? r.msg : 'No Details available';
                    Ext.MessageBox.alert(title,msg);
                    console.log(form, action)
                }
                });
            }
            ";
        $rowActionsFunctions['ORDER_EDIT_SAVE_COUPON'] = $js;
        $rowActions[] = array('iconCls' => 'ORDER_EDIT_SAVE_COUPON', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_ORDER_EDIT_SAVE_COUPON'));

        // coupon code löschen
        $js_rem = "
            var cmpId = order_edit_edit_couponbd.id;
            var cmp = Ext.getCmp(cmpId);
            var orders_id = ". $this->url_data['orders_id'] . ";
            var current_coupon_code = Ext.getCmp(cmpId).getForm().findField('current_coupon_code').getValue();
            var conn = new Ext.data.Connection();
            
            var orders_id = ". $this->url_data['orders_id'] . ";
            
            var mask = new Ext.LoadMask(Ext.getCmp(cmpId).getForm().el, {msg:'Moment'});
            mask.show();
            
            conn.request({
                url: 'adminHandler.php',
                method:'POST',
                params: {
                    pg:             'editCoupon',
                    load_section:   'order_edit_edit_coupon',
                    plugin:         'order_edit',
                    orders_id:      orders_id,
                    coupon_code: 'internal_coupon_code_remove',
                    current_coupon_code: current_coupon_code,
                    mode: 'REMOVE_COUPON',
                    sec: '".$_SESSION['admin_user']['admin_key']."'
                },
                success: function(responseObject)
                {
                    mask.hide();
                    var r = Ext.decode(responseObject.responseText);
                    if (!r.success)
                    {
                        Ext.MessageBox.alert('Error', r.msg);
                    }
                    //order_edit_productsds.reload();
                    contentTabs.getActiveTab().getUpdater().refresh();
                    order_edit_edit_coupongridEditForm.getForm().load({url:'adminHandler.php?load_section=order_edit_edit_coupon&plugin=order_edit&pg=edit_coupon&edit_id=1&orders_id=". $this->url_data['orders_id'] . "&modal=true&parentNode=order_edit_edit_couponRemoteWindow&get_singledata=1&sec=".$_SESSION['admin_user']['admin_key']."', waitMsg:'Loading',method: 'GET'});
                },
                failure: function(responseObject)
                {
                    mask.hide();
                    var title = responseObject.statusText ? 'Error '+responseObject.status : 'Error ';
                    var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                    Ext.MessageBox.alert(title,msg);
                    console.log(responseObject)
                }
            });";
        $rowActionsFunctions['ORDER_EDIT_REMOVE_COUPON'] = $js_rem;
        $rowActions[] = array('iconCls' => 'ORDER_EDIT_REMOVE_COUPON', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_ORDER_EDIT_REMOVE_COUPON'));


        if (count($rowActionsFunctions) > 0) {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }


		return $params;
	}

    function _get($ID = 0) {
        global $db;

        if ($this->position != 'admin') return false;

        global $order_edit_controller;
        $order = $order_edit_controller->getOrder();

        $couponCode = $current_couponCode_show = '';

        if(order_edit_controller::isCouponPluginActive())
        {
            $couponCode = $current_couponCode_show = '';
            $couponId = (int)$db->GetOne(
                "SELECT `coupon_id` FROM `" . DB_PREFIX . "_coupons_redeem` WHERE `order_id` = ?",
                array($order->oID)
            );
            if ($couponId)
            {
                $couponCode = $db->GetOne(
                    "SELECT `coupon_code` FROM `" . DB_PREFIX . "_coupons` WHERE `coupon_id` = ?",
                    array($couponId)
                );

                $oci = $order_edit_controller->getCouponForOrder($order->oID);
                if ($oci->isToken)
                {
                    $couponCode = $oci->xt_coupon_token['coupon_token_code'];
                }
                $table_data = new adminDB_DataRead(TABLE_COUPONS, TABLE_COUPONS_DESCRIPTION, null, 'coupon_id', '', '1', '', '');
                $dbdata = $table_data->getData($couponId);
                if (is_array($dbdata))
                {
                    $descData = $this->getCouponDescription($dbdata[0]);
                    $current_couponCode_show = $couponCode . '  (' . $descData['name'] . ')';
                }
            }
        }

        /*
        $removedCouponCode = '';
        $coupons = $_SESSION['order_edit_coupons'];
        if(!is_null($coupons) && is_array($coupons) && array_key_exists($order->oID, $coupons))
        {
            $oci = order_edit_controller::ociFromStdClass($coupons[$order->oID]);
            $removedCouponCode = $oci->xt_coupon['coupon_code'];
            if ($oci->isToken)
            {
                $removedCouponCode = $oci->xt_coupon_token['coupon_token_code'];
            }
            $table_data = new adminDB_DataRead(TABLE_COUPONS, TABLE_COUPONS_DESCRIPTION, null, 'coupon_id', '', '1', '', '');
            $dbdata = $table_data->getData($oci->xt_coupon['coupon_id']);
            if (is_array($dbdata))
            {
                $descData = $this->getCouponDescription($dbdata[0]);
                $removedCouponCode .= '  ('. $descData['name']. ')';
            }
        }
        */

        $data = array();
        $data[] = array(
            //'removed_coupon_code' => $removedCouponCode,
            'current_coupon_code' => $couponCode,
            'current_coupon_code_show' => $current_couponCode_show,
            'new_coupon_code_coupons' => '',
            'new_coupon_code_code' => '',
            'orders_id' => $this->url_data['orders_id']);

        $count_data = count($data);

        $obj = new stdClass();
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }

    function _set($data, $set_type = 'edit') {
        return false;
    }

    function editCoupon($data)
    {
        $r = new stdClass();
        $r->success = true;
        //die();
        if( ($data['mode'] == 'EDIT_COUPON' && !empty($data['current_coupon_code']))
            || $data['mode'] == 'REMOVE_COUPON'
        )
        {
            $data['mode'] = 'REMOVE_COUPON';
            $r_remove = $this->editCoupon_internal($data);
            if ($r_remove->errors)
            {
                $r->success = false;
                $r->msg = '';
                foreach($r_remove->errors as $error)
                {
                    $r->msg .= $error;
                }
                return json_encode($r);
            }
            $data['mode'] = 'EDIT_COUPON';
            $data['current_coupon_code'] = '';
        }
	    return $this->editCoupon_internal($data);
    }

	function editCoupon_internal($data) {
		global $db,$language,$filter, $tax;

        $r = new stdClass();
        $r->success = false;

        $oId = (int) $data['orders_id'];
        $coupon_code = trim($data['new_coupon_code_coupons']);
        if (!empty($data['new_coupon_code_code']))
        {
            $coupon_code = trim($data['new_coupon_code_code']);
        }
        else if ($data['coupon_code'] == 'internal_coupon_code_remove'){
            $coupon_code = trim($data['coupon_code']);
        }
        if ($oId)
        {
            global $db, $order_edit_controller;

            $order = $order_edit_controller->getOrder();

            $priceOverride = $_SESSION['order_edit_priceOverride'];
            if(!$priceOverride)
            {
            	$priceOverride = array();
            }
            else if (!$priceOverride[$order->oID])
            {
            	$priceOverride[$order->oID] = array();
            }
            if ($order->order_products)
            {
            	foreach($order->order_products as $p)
            	{
            		$priceOverride[$order->oID][$p['products_id']] = $p['products_price']['plain_otax'];
            	}
            }
            
            $_SESSION['order_edit_priceOverride'] = $priceOverride;

            $cart = new cart();
            $_SESSION['cart'] = $cart;

            // vorhandene hinzufügen
            if ($order->order_products)
            {
                foreach($order->order_products as $p)
                {
                    $_SESSION['cart']->_addCart( array(
                        'product' => $p['products_id'],
                        'qty' => $p['products_quantity'],
                        'customer_id' => $order->customer,
                    	'products_info' => unserialize($p['products_data']),
                        'orders_products_id' => $p['orders_products_id']
                    ));
                }
            }

            $_SESSION['cart']->_refresh();
            // coupons, payment und shipping anfügen
            $ctrlResult = order_edit_controller::setCartSubContent($order, $data['mode'], $coupon_code);
            if ($ctrlResult->errors)
            {
                $r->success = false;
                $r->msg = '';
                foreach($ctrlResult->errors as $error)
                {
                    $r->msg .= $error;
                }
                return $r;
            }

            // order speichern
            $order_edit_controller->setOrder($order, 'update');

			//order_edit_controller::updateProductOrderPercentageDiscount($order, $coupon_code);
            $r->success = true;
        }
        return $r;
	}

    function getCoupons()
    {
        $data = array();
        $table_data = new adminDB_DataRead(TABLE_COUPONS, TABLE_COUPONS_DESCRIPTION, null, 'coupon_id', 'coupon_status = 1', '', '', '');

        $db_data = $table_data->getData();

        if (is_array($db_data))
        {
            foreach ($db_data as $item)
            {
                $tmp_data = $this->getCouponDescription($item);
                $data[] = $tmp_data;
            }
        }
        return $data;
    }

    function getCouponDescription($dbItem)
    {
        global $language;

        $desc = ' - ';
        if (!$dbItem['coupon_percent'] && !$dbItem['coupon_free_shipping'])
        {
            $desc .= __text('TEXT_COUPON_TYPE_FIX') .' '. round($dbItem['coupon_amount'],2);
        }
        else if (!$dbItem['coupon_free_shipping'])
        {
            $desc .= __text('TEXT_COUPON_TYPE_PERCENT') .' '. round($dbItem['coupon_percent'],2) .'%';
        }
        else
        {
            $desc .= __text('TEXT_COUPON_TYPE_FREESHIPPING');
        }
        if ($dbItem['coupon_free_on_100_status'])
        {
            $desc .= ', '.__text('TEXT_COUPON_FREE_ON_100_STATUS');
        }
        if ($dbItem['coupon_minimum_order_value']!=0)
        {
            $desc .= ', '.__text('TEXT_COUPON_MINIMUM_ORDER_VALUE') .' '. round((float)$dbItem['coupon_minimum_order_value'],2);
        }
        $name = $dbItem['coupon_code'].$desc;
        if ($dbItem['coupon_description_' . $language->code])
        {
            $desc .= $dbItem['coupon_description_' . $language->code];
        }

        $tmp_data = array('id' => $dbItem['coupon_code'], 'name' => $name, 'desc' => $desc);

        return $tmp_data;
    }
	
	function _unset($id = 0) {
	    return false;
    }
}