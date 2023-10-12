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



class order_edit_add_products extends product {

    function _getParams() {
        global $language;

        $params = array();
        $header['products_id'] = array('type'=>'hidden', 'width' => 20);
        $header['products_name_'.$language->code] = array('type'=>'hidden');
        $header['products_name'] = array('type'=>'hidden');
        $header['products_model'] = array('type'=>'hidden');
        $header['products_quantity'] = array('type'=>'hidden');
        $header['products_price'] = array('type'=>'hidden');
        $header['products_preis_formated'] = array('type'=>'hidden');
        $header['products_status'] = array('type'=>'hidden');

        $params['display_checkCol']  = false;
        $params['display_editBtn']  = false;
        $params['display_deleteBtn']  = false;
        $params['display_newBtn']  = false;
        $params['display_GetSelectedBtn'] = false;

        $params['display_searchPanel']  = true;

        $params['gridType']  = 'EditGrid';

        $params['header']         = $header;
        $params['master_key']     = $this->_master_key;
        $params['default_sort']   = $this->_master_key;

        if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
            $params['include'] = array (
                'products_id',
                'products_name_'.$language->code,
                'products_model',
                'products_quantity',
                'products_preis_formated',
                'order_products_quantity',
                'products_order_price',
                'products_status');
        }

        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? ",sec:'".$_SESSION['admin_user']['admin_key']."'": '';
        // ---------------------------------------- row actions
        // add item
        $js = "
                grid.loadMask.show();
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php',
                    method:'POST',
                    params: {
                        pg:             'addOrderItem',
                        load_section:   'order_edit_add_products',
                        plugin:         'order_edit',
                        orders_id:      orders_id,
                        products_id: record.data.products_id,
                        order_products_quantity: record.data.order_products_quantity,
                        products_order_price: record.data.products_order_price".$add_to_url."
                    },
                    success: function(responseObject)
                    {
                        var r = Ext.decode(responseObject.responseText);
                        if (!r.success || null != r.msg)
                        {
                            Ext.MessageBox.alert('Error', r.msg);
                        }
                        order_edit_add_productsds.reload();
                        //contentTabs.getActiveTab().getUpdater().refresh();
                        contentTabs.getActiveTab().load('order_edit.php?pg=overview&parentNode=node_order&gridHandle=ordergridForm&edit_id='+r.orders_id);
                    },
                    failure: function(responseObject)
                    {
                        var title = responseObject.statusText ? 'Error '+responseObject.status : 'Error ';
                        var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                        Ext.MessageBox.alert(title,msg);
                        console.log(responseObject)
                    }
                });";

        $rowActionsFunctions['ORDER_EDIT_ADD_ITEM'] = $js;
        $rowActions[] = array('iconCls' => 'ORDER_EDIT_ADD_ITEM', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_ORDER_EDIT_ADD_ITEM'));

        // staffel berechnen
        $js = "
                grid.loadMask.show();
                var conn = new Ext.data.Connection();
                conn.request({
                    url: 'adminHandler.php',
                    method:'POST',
                    params: {
                        pg:             'calculateGraduatedPrice',
                        load_section:   'order_edit_add_products',
                        plugin:         'order_edit',
                        orders_id:      orders_id,
                        products_id: record.data.products_id,
                        order_products_quantity: record.data.order_products_quantity".$add_to_url."
                    },
                    success: function(responseObject)
                    {

                        var r = Ext.decode(responseObject.responseText);
                        if (!r.success || null != r.msg)
                        {
                            Ext.MessageBox.alert('Error', r.msg);
                            return;
                        }
                        //console.log(r.price);
                        record.set('products_order_price', r.price);
                        grid.loadMask.hide();
                    },
                    failure: function(responseObject)
                    {
                        var title = responseObject.statusText ? 'Error '+responseObject.status : 'Error ';
                        var msg = responseObject.statusText ? responseObject.statusText : 'No Details available';
                        Ext.MessageBox.alert(title,msg);
                        //console.log(responseObject);
                        grid.loadMask.hide();
                    }
                });";

        $rowActionsFunctions['ORDER_EDIT_CALCULATE_GRADUATED'] = $js;
        $rowActions[] = array('iconCls' => 'ORDER_EDIT_CALCULATE_GRADUATED', 'qtipIndex' => 'qtip1', 'tooltip' => __text('TEXT_ORDER_EDIT_CALCULATE_GRADUATED'));


        if (count($rowActionsFunctions) > 0) {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }

        return $params;
    }

    public function calculateGraduatedPrice($data)
    {
        $r = new stdClass();
        $r->success = false;

        global $order_edit_controller;
        $orderFields = $order_edit_controller->_orderFields;
        $cStatus = $order_edit_controller->_customers_status;

        $product = product::getProduct($data['products_id'],'default', $data['order_products_quantity'], $orderFields['language_code']);
        $product->buildData('default');

        if ((int)$cStatus->customers_status_show_price_tax)
        {
            $pop = $product->data['products_price']['plain'];
        }
        else {
            if (array_key_exists('old_plain_otax', $product->data['products_price']))
            {
                $pop = $product->data['products_price']['plain'];
            }
            else {
                $pop = $product->data['products_price']['plain_otax'];
            }
        }
        $r->price = $pop;

        $r->success = true;

        return json_encode($r);
    }

    public function addOrderItem($data)
    {
        global $tax;

        $result = new stdClass();
        $result->success = false;

        $oId = (int) $data['orders_id'];
        $opId = (int) $data['products_id'];
        if ($oId && $opId)
        {
            global $db, $order_edit_controller;
            $order = $order_edit_controller->getOrder();

            $cart = new cart();
            $_SESSION['cart'] = $cart;




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



            $priceOverride = $_SESSION['order_edit_priceOverride'];
            if(!$priceOverride)
            {
                $priceOverride = array();
            }
            else if (!$priceOverride[$oId])
            {
                $priceOverride[$oId] = array();
            }

            $op_price = $data['products_order_price'];
            $add_product = product::getProduct($opId, 'default');
            if ($order_edit_controller->_customers_status->customers_status_show_price_tax)
            {
                $tax_rate = $tax->data[$add_product->data["products_tax_class_id"]];
                $op_price = $op_price / (1 + $tax_rate / 100);
            }

            // haben wir einen prozentgutschein an der order ?
            if(order_edit_controller::isCouponPluginActive())
            {
                $oci = $order_edit_controller::getCouponForOrder($order->oID);
                if($oci && (float)$oci->xt_coupon['coupon_percent'] > 0)
                {
                    $coupon = new xt_coupons();
                    $cpic = $coupon->_get_coupon_products_in_cart($oci->xt_coupon["coupon_id"]);
                    if(in_array($opId, $cpic['all']))
                    {
                        $op_price = (1 - (float)$oci->xt_coupon['coupon_percent']/100) * $op_price;
                    }
                }
            }

            $priceOverride[$oId][$opId] = $op_price;
            $_SESSION['order_edit_priceOverride'] = $priceOverride;

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
            global $db;

            // neuen artikel adden
            $_SESSION['cart']->_addCart(array(
                'product' => $opId,
                'qty' => $data['order_products_quantity'],
                'customer_id' => $order->customer
            ));

            $_SESSION['cart']->_refresh();
            // payment,shipping,coupon anfügen
            $r = order_edit_controller::setCartSubContent($order);

            //$_SESSION['cart']->_refresh();

            if ($r->couponRemoved)
            {
                $result->msg = __text('TEXT_ORDER_EDIT_WARNING_COUPON_REMOVED');
                if ($r->errors)
                {
                    foreach($r->errors as $e)
                    {
                        $result->msg .= $e;
                    }
                }
            }

            // order speichern
            $order_edit_controller->setOrder($order);

            $result->success = true;
            $result->orders_id = $oId;
        }
        return json_encode($result);
    }


    function _get($ID = 0)
    {
        global $db, $xtPlugin;

        if ($this->position != 'admin') return false;

        global $order_edit_controller;
        $orderFields = $order_edit_controller->_orderFields;

        if ($this->url_data['get_data'])
        {

            $sqlExludeIds = "SELECT group_concat(`products_id`) FROM ".TABLE_ORDERS_PRODUCTS ." WHERE `orders_id`=?";
            $exludeIds = $db->GetOne($sqlExludeIds, array($this->url_data['orders_id']));

            $plg = new plugin();

            $sql_where = '';
            $col_exists = $plg->_FieldExists('products_master_flag', TABLE_PRODUCTS);
            if($col_exists)
            {
                $sql_where = " (products_master_flag is null OR products_master_flag=0 OR products_master_flag='') ";
            }

            if (!empty($exludeIds)){
                if($col_exists) $sql_where .= ' AND ';
                $sql_where .= ' products_id NOT IN ('.$exludeIds.') ';
            }

            if($this->url_data['query'])
            {
                $search_result = $this->_getSearchIDs($this->url_data['query']);
                if(is_array($search_result) && count($search_result)>0)
                {
                    if (!empty($sql_where))
                    {
                        $sql_where .=  ' and ';
                    }
                    $sql_where .= " products_id IN (".implode(',', $search_result).")";
                }
            }

            $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, $this->sql_limit, $this->perm_array);
            $data = $table_data->getData(null, $orderFields["shop_id"]);

            global $order_edit_controller;
            $cStatus = $order_edit_controller->_customers_status;
            foreach ($data as $k => $product)
            {
                $data[$k]['order_products_quantity'] = 1;
                $product = product::getProduct($data[$k]['products_id'],'default', $data[$k]['order_products_quantity'], $orderFields['language_code']);

                // tweak des store_handler, es soll immer der aus der bestellung sein
                global $store_handler;
                $storeId = $store_handler->shop_id;
                $store_handler->shop_id = $order_edit_controller->_orderFields['shop_id'];

                $product->buildData('default');

                // store_handler tweak entfernen
                $store_handler->shop_id = $storeId;

                $data[$k]['products_quantity'] = $product->data['products_quantity'];

                if ((int)$cStatus->customers_status_show_price_tax)
                {
                    $pop = $product->data['products_price']['plain'];
                }
                else {
                    if (array_key_exists('old_plain_otax', $product->data['products_price']))
                    {
                        $pop = $product->data['products_price']['plain'];
                    }
                    else {
                        $pop = $product->data['products_price']['plain_otax'];
                    }
                }

                $data[$k]['products_order_price'] = $pop;

                $data[$k]['products_preis_formated'] = $product->data['products_price']['formated'];
                if (is_array($product->data['group_price']) && is_array($product->data['group_price']['prices']))
                {
                    global $price;
                    foreach($product->data['group_price']['prices'] as $g_price )
                    {
                        $g_price['price'] = $price->_AddTax($g_price['price'], $product->data['products_tax_rate']);
                        $data[$k]['products_preis_formated'] .='<br />'.$g_price['qty'].' => '. round($g_price['price'],2);
                    }
                }

            }
        }
        else
        {
            $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', '', $this->perm_array);
            $data = $table_data->getHeader();
            //unset($_SESSION['order_edit_priceOverride']);
        }

        if($table_data->_total_count!=0 || !$table_data->_total_count)
            $count_data = $table_data->_total_count;
        else
            $count_data = count($data);

        $obj = new stdClass();
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }

    function _set($id, $set_type = 'edit')
    {
        $data = array();
        $data['products_id'] = (int)$this->url_data['products_id'];
        $data['products_id_cross_sell'] = (int)$id;

        $o = new adminDB_DataSave($this->_table_xsell, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        return $obj;
    }

    function _unset($id = 0) {
        global $db;
        $pID=(int)$this->url_data['products_id'];
        $id=(int)$id;

        if (!$id || !$pID || $this->position != 'admin') return false;
        $db->Execute(
            "DELETE FROM ". $this->_table_xsell ." WHERE products_id_cross_sell =? and products_id = ?",
            array($id, $pID)
        );
    }
}