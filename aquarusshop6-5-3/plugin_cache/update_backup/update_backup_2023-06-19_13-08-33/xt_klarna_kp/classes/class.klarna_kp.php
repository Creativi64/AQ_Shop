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

//error_log('in '.__FILE__);

class klarna_kp
{
    // klarna backend urls
    const EU_TEST_URL_BACKEND   = "https://orders.playground.eu.portal.klarna.com";
    const EU_URL_BACKEND        = "https://orders.eu.portal.klarna.com";

    private $_xt_order_id = false;
    private static $_instance = false;

    private function __construct()
    {
        return $this;
    }

    public static function getInstance()
    {
        if(!is_object(self::$_instance))
        {
            self::$_instance = new klarna_kp();
        }
        return self::$_instance;
    }



    public function setXtOrderId($xt_order_id)
    {
        $this->_xt_order_id = $xt_order_id;
        return $this;
    }
    public function getXtOrderId()
    {
        return $this->_xt_order_id;
    }

    public static function getBackendBaseUrl($force_test = false)
    {
        if(!defined('_KLARNA_CONFIG_KP_TESTMODE'))
            return self::EU_TEST_URL_BACKEND;
        if(constant('_KLARNA_CONFIG_KP_TESTMODE')==1 || $force_test)
        {
            return self::EU_TEST_URL_BACKEND;
        }
        return self::EU_URL_BACKEND;
    }

    public static function getBaseUrl($force_test = false)
    {
        if(!defined('_KLARNA_CONFIG_KP_TESTMODE'))
            return \Klarna\Rest\Transport\ConnectorInterface::EU_TEST_BASE_URL;
        if(constant('_KLARNA_CONFIG_KP_TESTMODE')==1 || $force_test)
        {
            return \Klarna\Rest\Transport\ConnectorInterface::EU_TEST_BASE_URL;
        }
        return \Klarna\Rest\Transport\ConnectorInterface::EU_BASE_URL;
    }

    public function getConnector($store_id = null)
    {
        global $db, $store_handler, $customers_status;

        $mid = constant('_KLARNA_CONFIG_KP_MID');
        $pwd = constant('_KLARNA_CONFIG_KP_PWD');

        $test_mode = false;
        if(USER_POSITION == 'store')
        {
            $test_group = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store_handler->shop_id. " WHERE config_key = '_KLARNA_CONFIG_KP_TESTMODE_CUSTOMER_GROUP' ");
            if($test_group == $customers_status->customers_status_id)
            {
                $test_mode = true;
            }
        }

        if(constant('_KLARNA_CONFIG_KP_TESTMODE') == 1 || $test_group > 0)
        {
            $mid = constant('_KLARNA_CONFIG_KP_MID_TEST');
            $pwd = constant('_KLARNA_CONFIG_KP_PWD_TEST');
        }

        if((is_object($this) && $this->_xt_order_id) || $store_id)
        {
            global $db;
            if(empty($store_id))
                $store_id = $db->GetOne("SELECT o.shop_id FROM ".TABLE_ORDERS." o LEFT JOIN ".TABLE_MANDANT_CONFIG." s ON s.shop_id = o.shop_id WHERE o.orders_id=?", array($this->_xt_order_id));
            if($store_id)
            {
                $key_mid = '_KLARNA_CONFIG_KP_MID';
                $key_pwd = '_KLARNA_CONFIG_KP_PWD';

                $test_mode_order = $db->GetOne("SELECT o.kp_order_test_mode FROM ".TABLE_ORDERS." o WHERE o.orders_id=?", array($this->_xt_order_id));
                if($test_mode_order == "1" || $test_mode_order == "0")
                {
                    if($test_mode_order == "1")
                    {
                        $test_mode = true;
                    }
                }
                else {
                    // weiter nach altem muster
                    $test_mode = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store_id. " WHERE config_key = '_KLARNA_CONFIG_KP_TESTMODE' ");
                }

                if($test_mode)
                {
                    $key_mid = '_KLARNA_CONFIG_KP_MID_TEST';
                    $key_pwd = '_KLARNA_CONFIG_KP_PWD_TEST';
                }

                $mid = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store_id. " WHERE config_key = '".$key_mid."' ");
                $pwd = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store_id. " WHERE config_key = '".$key_pwd."' ");
            }
            else {
                throw new Exception('No store found for xt-order ['.$this->_xt_order_id.']');
            }
        }

        $module_version = $db->GetOne("SELECT version FROM ".TABLE_PLUGIN_PRODUCTS." p WHERE p.code='xt_klarna_kp'");
        $userAgent = \Klarna\Rest\Transport\UserAgent::createDefault();
        $userAgent->setField('Shop','XT-Commerce',_SYSTEM_VERSION);
        $userAgent->setField('Module','xt_klarna_kp',$module_version);


        $connector = \Klarna\Rest\Transport\Connector::create(
            $mid,
            $pwd,
            self::getBaseUrl($test_mode),
            $userAgent
        );
        return $connector;
    }

    public static function setKlarnaOrderInXt($kp_order, $xt_order_id, $set_test_mode = null)
    {
        global $db;
        if(get_class($kp_order) == 'ArrayObject')
            $arr = $kp_order->getArrayCopy();
        else
            $arr = $kp_order;
        $json = json_encode($arr, true);
        $db->Execute("UPDATE ".TABLE_ORDERS." SET kp_order_id=?, kp_order_data=?, kp_order_ref=?, kp_order_status=?, kp_order_fraud_status=? WHERE orders_id=?",
            array($kp_order['order_id'], $json, $kp_order['klarna_reference'], $kp_order['status'], $kp_order['fraud_status'], $xt_order_id));

        if(!is_null($set_test_mode) && is_bool($set_test_mode))
        {
            $db->Execute("UPDATE ".TABLE_ORDERS." SET kp_order_test_mode=? WHERE orders_id=?",
                array($set_test_mode ? 1 : 0, $xt_order_id));
        }

    }

    public static function getKlarnaOrderFromXt($xt_order_id, $kp_order_id = false)
    {
        global $db;
        $where_col = 'orders_id';
        $params = array($xt_order_id);
        if($kp_order_id != false) {
            $where_col = 'kp_order_id';
            $params = array($kp_order_id);
        }
        $json = $db->GetOne("SELECT kp_order_data FROM ".TABLE_ORDERS." WHERE $where_col=?", $params);
        $ar = json_decode($json,true);
        //$ao = new ArrayObject();
        //$ao->exchangeArray(json_decode($json,true));
        return $ar;
    }

    public static function getKlarnaOrderId($xt_order_id)
    {
        global $db;
        $where_col = 'orders_id';
        $params = array($xt_order_id);

        $klarna_order_id = $db->GetOne("SELECT kp_order_id FROM ".TABLE_ORDERS." WHERE $where_col=?", $params);
        return $klarna_order_id;
    }

    public static function getMappedXtOrderStatus($kp_order_status, $xt_order_store_id)
    {
        global $db;
        $where_col = 'config_key';
        $params = array(strtoupper('_KLARNA_CONFIG_STATUS_'.$kp_order_status));

        $xt_order_status = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$xt_order_store_id." WHERE $where_col=?", $params);
        return $xt_order_status;
    }

    public static function setKlarnaOrderIdInXt($kp_order_id, $xt_order_id, $sync = false)
    {
        global $db;
        $db->Execute("UPDATE ".TABLE_ORDERS." SET kp_order_id=? WHERE orders_id=?", array($kp_order_id, $xt_order_id));
        if($sync) self::setXtOrderIdInKlarna($kp_order_id, $xt_order_id);
    }

    public static function setXtOrderIdInKlarna($kp_order_id, $xt_order_id, $sync = false)
    {
        global $shop;
        $domain = !empty($shop->domain) ? $shop->domain : false;

        self::setMerchantReference($kp_order_id, $xt_order_id, $domain);
        if($sync) self::setKlarnaOrderIdInXt($kp_order_id, $xt_order_id);
    }


    public static function createCapture($kp_order_id, $amount, $description = '', array $shipping_info = array(), array $order_lines = array())
    {
        try
        {
            $amount = round($amount, 0);
            $connector = self::getInstance()->getConnector();

            $orderPath = \Klarna\Rest\OrderManagement\Order::$path;
            $capture = new \Klarna\Rest\OrderManagement\Capture($connector, $orderPath.'/'.$kp_order_id);
            $data = array('captured_amount' => $amount, 'description' => $description);
            if(count($shipping_info))
            {
                $data['shipping_info'] = $shipping_info;
            }
            if(count($order_lines))
            {
                $data['order_lines'] = $order_lines;
            }


            $capture->create($data);
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $data, $ex);
            throw $ex;
        }
    }

    public static function addShippingInfo($kp_order_id, $capture_id, array $shipping_infos)
    {
        try
        {
            if (count($shipping_infos))
            {
                $connector = self::getInstance()->getConnector();

                $orderPath = \Klarna\Rest\OrderManagement\Order::$path;
                $capture = new \Klarna\Rest\OrderManagement\Capture($connector, $orderPath . '/' . $kp_order_id, $capture_id );
                $capture->addShippingInfo(array('shipping_info' => $shipping_infos));
            }
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(),
                array('kp_oder_id' => $kp_order_id, 'capture_id' => $capture_id, 'shipping_info' => $shipping_infos),
            $ex);
            throw $ex;
        }
    }

    public static function doRefund($kp_order_id, $amount, $description = '', $order_lines = array())
    {
        try
        {
            $amount = round($amount, 0);
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);
            $data = array('refunded_amount' => $amount, 'description' => $description);
            if(count($order_lines))
            {
                $data['order_lines'] = $order_lines;
            }
            $order->refund($data);
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $data, $ex);
            throw $ex;
        }
    }

    public static function doRelease($kp_order_id)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);

            $order->releaseRemainingAuthorization();
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), array(), $ex);
            throw $ex;
        }
    }

    public static function cancelOrder($kp_order_id)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);

            $order->cancel();
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), array(), $ex);
            throw $ex;
        }
    }

    public static function updateCustomerDetails($kp_order_id, $shipping_address = array(), $billing_address = array())
    {
        try
        {
            $data = array();
            if(is_array($shipping_address) && count($shipping_address))
            {
                $data['shipping_address'] = $shipping_address;
            }
            if(is_array($billing_address) && count($billing_address))
            {
                $data['billing_address'] = $billing_address;
            }
            if(count($data))

            {
                $connector = self::getInstance()->getConnector();

                $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);

                $order->updateCustomerDetails($data);
            }
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), array(), $ex);
            throw $ex;
        }
    }


    public static function acknowledgeOrder($kp_order_id)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);
            $order->acknowledge();
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), array(), $ex);
            throw $ex;
        }
    }


    public static function setMerchantReference($kp_order_id, $ref1, $ref2 = '')
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);
            $data = array(
                'merchant_reference1' => $ref1,
                'merchant_reference2' => $ref2,
            );
            $order->updateMerchantReferences($data);
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $data, $ex);
            throw $ex;
        }
    }


    public static function triggerResendCustomerCommunication($kp_order_id, $capture_id)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);
            $capture = new \Klarna\Rest\OrderManagement\Capture($connector, $order->getLocation(), $capture_id);
            $capture->triggerSendout();
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), array(), $ex);
            throw $ex;
        }
    }

    public static function extendAuthorizationTime($kp_order_id)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kp_order_id);
            $order->extendAuthorizationTime();
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), array(), $ex);
            throw $ex;
        }
    }

    public static function createPaymentsOrderFromSession($addData = [], $sessionId, customer $customer, $authToken, $b2b = false, array &$errors)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $sessions = $order = new \Klarna\Rest\Payments\Sessions($connector, $sessionId);
            $paymentsSession = $sessions->fetch();
            $paymentsSession = $paymentsSession->getArrayCopy();

            // remove some
            unset($paymentsSession['status']);
            unset($paymentsSession['payment_method_categories']);
            unset($paymentsSession['expires_at']);
            unset($paymentsSession['client_token']);

            // add some
            foreach ($addData as $k => $v)
            {
                $paymentsSession[$k] = $v;
            }

            self::setAddresses($customer, $paymentsSession);
            self::setCustomer($customer, $paymentsSession, $b2b);

            $order = new \Klarna\Rest\Payments\Orders($connector, $authToken);
            $createdOrder = $order->create($paymentsSession);

            return $createdOrder;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $paymentsSession, $ex);
            throw $ex;
        }
    }

    /**
     * @param cart $cart
     * @param customer $customer
     * @param $customers_status
     * @param array $urls
     * @param false $b2b
     * @param array $errors
     * @return \Klarna\Rest\Payments\Sessions
     * @throws KlarnaApiException
     */
    public static function createPaymentsSession(cart $cart, customer $customer, $customers_status, array $urls, $b2b = false, array &$errors)
    {
        global $tax, $language, $currency, $xtLink, $xtPlugin;

        $order_data = array();
        $order_amount = 0;
        $order_tax_amount = 0;

        $errors = array();

        /**
         * set cart content / line items
         */
        self::setCartContent($cart, $customers_status,$order_data,  $order_amount, $order_tax_amount);

        /**
         * set cart sub content / fees / discounts ...
         */
        self::setCartSubContent($cart,$order_data,  $order_amount, $order_tax_amount);

        /**
         * set addresses  country only
         *
         */
        self::setAddressesMinimum($customer, $order_data);

        /**
         * set customer (not detailed, w/o dob)
         *
         */
        self::setCustomer($customer, $order_data, $b2b, false);


        /**
         *  purchase currency
         */
        $order_data['purchase_currency'] = strtolower($currency->code);

        /**
         *  purchase country
         */
        if($customer->customers_id)
        {
            if (is_array($customer->customer_payment_address) && count($customer->customer_payment_address))
            {
                $order_data['purchase_country'] = strtolower($customer->customer_payment_address['customers_country_code']);
            }
        }
        if(empty($order_data['purchase_country']))
        {
            $order_data['purchase_country'] = !empty($billing_country_code) ? $billing_country_code : strtolower(_STORE_COUNTRY);
        }


        $order_data['order_amount'] = $order_amount;
        $order_data['order_tax_amount'] = $order_tax_amount;

        /**
         * set language
         */
        self::setLanguageData($customer, $order_data);

        /**
         * set gui settings
         */
        self::setGuiData($customer, $order_data);

        /**
         * set merchant urls / terms/callback/etc
         */
        self::setMerchantUrls($urls, $order_data);

        try
        {
            $connector = self::getInstance()->getConnector();

            $sessions = new \Klarna\Rest\Payments\Sessions($connector);
            $session = $sessions->create($order_data);
            //$session = $sessions->fetch();
            return $session;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $order_data, $ex);
            $kex = new KlarnaApiException($ex->getMessage(), 0, $ex);
            $kex->setData($order_data);
            throw $kex;
        }
    }

    public static function updatePaymentsSession($kp_session_id, cart $cart, customer $customer, $customers_status, array &$errors)
    {
        global $tax, $language, $currency, $xtLink, $xtPlugin;

        $order_data = array();
        $order_amount = 0;
        $order_tax_amount = 0;

        $errors = array();

        /**
         * set cart content / line items
         */
        self::setCartContent($cart, $customers_status,$order_data,  $order_amount, $order_tax_amount);

        /**
         * set cart sub content / fees / discounts ...
         */
        self::setCartSubContent($cart,$order_data,  $order_amount, $order_tax_amount);




        /**
         *  purchase currency
         */
        $order_data['purchase_currency'] = strtolower($currency->code);

        /**
         *  purchase country
         */

        $order_data['purchase_country'] = strtolower(_STORE_COUNTRY);
        /**/
        if($customer->customers_id)
        {
            if (is_array($customer->customer_payment_address) && count($customer->customer_payment_address))
            {
                $order_data['purchase_country'] = strtolower($customer->customer_payment_address['customers_country_code']);
            }
        }
        if(empty($order_data['purchase_country']))
        {
            $order_data['purchase_country'] = !empty($billing_country_code) ? $billing_country_code : strtolower(_STORE_COUNTRY);
        }


        $order_data['order_amount'] = $order_amount;
        $order_data['order_tax_amount'] = $order_tax_amount;



        try
        {
            $connector = self::getInstance()->getConnector();

            $sessions = new \Klarna\Rest\Payments\Sessions($connector, $kp_session_id);
            $session = $sessions->update($order_data);
            $session = $sessions->fetch();
            return $session;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $order_data, $ex);
            throw $ex;
        }
    }

    public static function setSessionLanguage($language_code, $session_id)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $update_data = array();
            self::setLanguageData($language_code, $update_data);

            $sessions = new \Klarna\Rest\Payments\Sessions($connector, $session_id);
            $sessions->update($update_data);
            $session =  $sessions->fetch();

            return $session;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    public static function getOrder($id)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $id);

            $order->fetch();
            return $order;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    public static function setNewOrderAmount($kco_order_id, $new_total_order_amount, $order_lines)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $order = new \Klarna\Rest\OrderManagement\Order($connector, $kco_order_id);

            $r = $order->updateAuthorization(array('order_amount' => $new_total_order_amount, 'order_lines' => $order_lines));

        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    private static function setCartContent($cart, $customers_status, array &$order_data, &$order_amount, &$order_tax_amount)
    {
        global $store_handler;

        foreach ($cart->show_content as $sc)
        {
            $total_amount       = round($sc['products_final_price']['plain'] * 100,0);
            $total_tax_amount   = round($sc['products_final_tax']['plain'] * 100,0);

            $unit_price = round($sc['products_price']['plain'] * 100,0);

            if($customers_status->customers_status_show_price_tax == 0 && $customers_status->customers_status_add_tax_ot == 1)
            {
                $total_amount += $total_tax_amount;

                $unit_price = round($sc['products_price']['plain'] * (1 + $sc['products_tax_info']['tax']/100) * 100,0);
            }

            $total_discount_amount = 0;
            if($sc['_cart_discount'] > 0)
            {
                $unit_price = round ($sc['_original_products_price']['plain'] * 100, 0);
                if($customers_status->customers_status_show_price_tax == 0 && $customers_status->customers_status_add_tax_ot == 1)
                {
                    $unit_price = round($sc['_original_products_price']['plain'] * (1 + $sc['products_tax_info']['tax']/100) * 100,0);
                }
                $total_discount_amount  = $unit_price * $sc['products_quantity'] - $total_amount;
            }

            $product_identifiers = null;
            $identifiers = array();
            if(!empty($sc['products_ean']))
            {
                $identifiers["global_trade_item_number"] = preg_replace('/[^a-zA-Z0-9]/', '' , $sc['products_ean']);
            }
            if(!empty($sc['products_mpn']))
            {
                $identifiers["manufacturer_part_number"] = preg_replace('/[^a-zA-Z0-9]/', '' , $sc['products_mpn']);
            }
            $mainCatId = self::getProductsMainCategoryId($sc['products_id'], $store_handler->shop_id);
            if ($mainCatId)
            {
                global $category;
                //$path = $category->getBreadCrumbNavigation($cat_id);
                $path = $category->getNavigationPath($mainCatId);
                $path = array_reverse($path);
                foreach ($path as $k => $v)
                {
                    $path[$k] = $v['categories_name'];
                }
                $identifiers["category_path"] = implode(' > ', $path);
            }

            if(count($identifiers))
            {
                $product_identifiers = $identifiers;
            }

            $tax_rate = round($sc['products_tax_info']['tax'] * 100,0);
            if($customers_status->customers_status_show_price_tax == 0 && $customers_status->customers_status_add_tax_ot == 0)
            {
                $tax_rate = 0;
            }

            $order_data['order_lines'][] = array(
                "type"          => $sc['products_digital'] == 0 ? "physical" : "digital",
                "reference"     =>  substr($sc['products_model'], 0 , 64),
                "name"          => $sc['products_name'],
                "quantity"      => $sc['products_quantity'],
                "quantity_unit" => $sc['products_unit_name'],
                "unit_price"    => $unit_price,
                "tax_rate"      => $tax_rate,
                "total_amount"  => $total_amount,
                "total_tax_amount" => $total_tax_amount,
                "total_discount_amount" => $total_discount_amount ,
                "product_identifiers" => $product_identifiers
            );

            if (_KLARNA_CONFIG_SEND_EXTENDED_PRODUCT_INFO == 1)
            {
                $order_data['order_lines'][count($order_data['order_lines'])-1]['product_url'] = $sc['products_link'];

                if(!empty($sc['products_image']) && $sc['products_image'] != 'product:noimage.gif')
                {
                    $params = array('img' => $sc['products_image'], 'type' => 'm_info', 'path_only' => true, 'return' => true);
                    $img_url = image::getImgUrlOrTag($params, new stdClass());
                    $order_data['order_lines'][count($order_data['order_lines'])-1]['image_url'] = $img_url;
                }
            }

            if($sc['manufacturers_id'])
            {
                global $manufacturer;
                $man_data = $manufacturer->getManufacturerData($sc['manufacturers_id']);
                if(!empty($man_data['manufacturers_name']))
                {
                    $order_data['order_lines'][count($order_data['order_lines'])-1]['product_identifiers']['brand'] = $man_data['manufacturers_name'];
                }
            }
            $order_amount       += $total_amount;
            $order_tax_amount   += $total_tax_amount;
        }
    }

    private static function getProductsMainCategoryId($products_id, $store_id)
    {
        global $db;
        $sql = "SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                WHERE master_link=1 and products_id=? and store_id=?";
        $mainCat = $db->GetOne($sql, array($products_id,$store_id));
        return $mainCat;
    }

    private static function setCartSubContent($cart, array &$order_data, &$order_amount, &$order_tax_amount)
    {
        global $customers_status, $xtPlugin;

        foreach ($cart->show_sub_content as $k => $sc)
        {
            if(in_array($k, array(/*'shipping',*/ 'payment'))) continue;

            $total_amount       = round($sc['products_final_price']['plain'] * 100,0);
            $total_tax_amount   = round($sc['products_final_tax']['plain'] * 100,0);

            $unit_price = round($sc['products_price']['plain'] * 100,0);

            if($customers_status->customers_status_show_price_tax == 0 && $customers_status->customers_status_add_tax_ot == 1)
            {
                $total_amount += $total_tax_amount;

                $unit_price += $total_tax_amount;
            }

            $tax_rate = $sc["products_tax_value"];
            switch($k)
            {
                case 'xt_coupon':
                    $type = 'discount';
                    break;
                case 'shipping':
                    $type = 'shipping_fee';
                    break;
                default:
                    $type = 'surcharge';
            }

            ($plugin_code = $xtPlugin->PluginCode('class.klarna_kp.php:setCartSubContent_foreach')) ? eval($plugin_code) : false;

            $order_data['order_lines'][] = array(
                "type"          => $type,
                "reference"     =>  substr($sc['products_model'], 0 , 64),
                "name"          => $sc['products_name'],
                "quantity"      => $sc['products_quantity'] ? $sc['products_quantity'] : 1,
                "quantity_unit" => $sc['products_unit_name'],
                "unit_price"    => $unit_price,
                "tax_rate"      => round($tax_rate * 100,0),
                "total_amount"  => $total_amount,
                "total_tax_amount" => $total_tax_amount
            );
            $order_amount       += $total_amount;
            $order_tax_amount   += $total_tax_amount;
        }
    }

    public static function setCustomer(customer $customer, array &$order_data, $b2b = false, $detailed = true)
    {
        if($b2b)
        {
            $order_data['customer']['type'] = 'organization';
            if(!empty($customer->customer_info['customers_vat_id'])) $order_data['customer']['vat_id'] = $customer->customer_info['customers_vat_id'];
        }
        else
            $order_data['customer']['type'] = 'person';

        if ($detailed && $customer->customers_id && $customer->customer_payment_address['customers_dob'] != false)
        {
            try {
                $format = preg_replace("/([a-zA-z])\\1+/", "$1", _STORE_ACCOUNT_DOB_FORMAT);
                $format = str_replace('y', 'Y', $format);
                $date = DateTime::createFromFormat($format, $customer->customer_payment_address['customers_dob']);
                if(is_object($date))
                {
                    $date_s = $date->format('Y-m-d');/**/
                    $order_data['customer']['date_of_birth'] = $date_s;
                }
            }
            catch(Exception $e){}
        }
    }

    public static function setAddresses(customer $customer, array &$order_data)
    {
        if ($customer->customers_id)
        {
            /**
             * set payment address
             */
            $addr = $customer->customer_payment_address;
            if (is_array($addr) && count($addr))
            {
                $country_code = strtolower($addr['customers_country_code']);
                $salut = self::getSalutation($country_code, $addr['customers_gender']);

                $order_data['billing_address'] = array(
                    'title' => $salut,
                    'given_name' => $addr['customers_firstname'],
                    'family_name' => $addr['customers_lastname'],
                    'email' => $_SESSION['customer']->customer_info['customers_email_address'],
                    'street_address' => $addr['customers_street_address'],
                    'postal_code' => $addr['customers_postcode'],
                    'city' => $addr['customers_city'],
                    'region' => $addr['customers_federal_state_code'],
                    'phone' => !empty($addr['customers_mobile_phone']) ? $addr['customers_mobile_phone'] : $addr['customers_phone'],
                    'country' => $country_code,
                    'organization_name' => $addr["customers_company"]
                );

                foreach($order_data['billing_address'] as $k => $v)
                {
                    if(empty($v)) $order_data['billing_address'][$k] = null;
                }
            }

            /**
             * set shipping address
             */
            $addr = $customer->customer_shipping_address;
            if (is_array($addr) && count($addr))
            {
                $country_code = strtolower($addr['customers_country_code']);
                $salut = self::getSalutation($country_code, $addr['customers_gender']);

                $order_data['shipping_address'] = array(
                    'title' => $salut,
                    'given_name' => $addr['customers_firstname'],
                    'family_name' => $addr['customers_lastname'],
                    'email' => $_SESSION['customer']->customer_info['customers_email_address'],
                    'street_address' => $addr['customers_street_address'],
                    'postal_code' => $addr['customers_postcode'],
                    'city' => $addr['customers_city'],
                    'region' => $addr['customers_federal_state_code'],
                    'phone' => !empty($addr['customers_mobile_phone']) ? $addr['customers_mobile_phone'] : $addr['customers_phone'],
                    'country' => $country_code,
                    'organization_name' => $addr["customers_company"]
                );

                foreach($order_data['shipping_address'] as $k => $v)
                {
                    if(empty($v)) $order_data['shipping_address'][$k] = null;
                }
            }
        }
    }

    public static function setAddressesMinimum(customer $customer, array &$order_data)
    {
        if ($customer->customers_id)
        {
            /**
             * set payment address
             */
            $addr = $customer->customer_payment_address;
            if (is_array($addr) && count($addr))
            {
                $country_code = strtolower($addr['customers_country_code']);

                $order_data['billing_address'] = array(
                    'country' => $country_code
                );
            }

            /**
             * set shipping address
             */
            $addr = $customer->customer_shipping_address;
            if (is_array($addr) && count($addr))
            {
                $country_code = strtolower($addr['customers_country_code']);

                $order_data['shipping_address'] = array(
                    'country' => $country_code,
                );
            }
        }
    }

    private static function getSalutation($country_code, $gender)
    {
        $salut = '';
        switch ($gender)
        {
            case 'f':
                switch ($country_code)
                {
                    case 'de':
                    case 'at':
                    case 'ch':
                        $salut = 'Frau';
                        break;
                    case 'uk':
                        $salut = 'Ms';
                        break;
                    case 'nl':
                        $salut = 'Mevr.';
                        break;
                    default:
                        $salut = 'Mr';
                }
                break;
            case 'm':
                switch ($country_code)
                {
                    case 'de':
                    case 'at':
                    case 'ch':
                        $salut = 'Herr';
                        break;
                    case 'uk':
                        $salut = 'Mr';
                        break;
                    case 'nl':
                        $salut = 'Dhr.';
                        break;
                    default:
                        $salut = 'Mr';
                }
                break;
            case 'c':
                switch ($country_code)
                {
                    case 'de':
                    case 'at':
                    case 'ch':
                        $salut = 'Frau';
                        break;
                    case 'uk':
                        $salut = 'Ms';
                        break;
                    case 'nl':
                        $salut = 'Mevr.';
                        break;
                    default:
                        $salut = 'Mr';
                }
                break;
            default:
                break;
        }

        return $salut;
    }

    private static function setLanguageData($customer, &$order_data)
    {
        global $language;

        $addr = $customer->customer_payment_address;
        if (is_array($addr) && count($addr))
        {
            $country_code = strtolower($addr['customers_country_code']);

            switch($country_code)
            {
                case 'de':
                    $order_data['locale'] = 'de-de';
                    break;
                case 'at':
                    $order_data['locale'] = 'de-at';
                    break;
                case 'gb':
                    $order_data['locale'] = 'en-gb';
                    break;
                case 'us':
                    $order_data['locale'] = 'en-us';
                    break;
                case 'dk':
                    $order_data['locale'] = 'da-dk';
                    break;
                case 'fi':
                    $order_data['locale'] = 'fi-fi';
                    break;
                case 'nl':
                    $order_data['locale'] = 'nl-nl';
                    break;
                case 'no':
                    $order_data['locale'] = 'nb-no';
                    break;
                case 'se':
                    $order_data['locale'] = 'sv-se';
                    break;
                case 'ch':
                    switch($language->code)
                    {
                        case 'it':
                            $order_data['locale'] = 'it-ch';
                            break;
                        case 'fr':
                            $order_data['locale'] = 'fr-ch';
                            break;
                        default:
                            $order_data['locale'] = 'de-ch';
                    }
                    break;
                default:
                    $order_data['locale'] = 'en-gb';
            }
        }
        else {
            switch($language->code)
            {
                case 'de':
                    $order_data['locale'] = 'de-de';
                    break;
                case 'en':
                    $order_data['locale'] = 'en-gb';
                    break;
                case 'da':
                    $order_data['locale'] = 'da-dk';
                    break;
                case 'fi':
                    $order_data['locale'] = 'fi-fi';
                    break;
                case 'nl':
                    $order_data['locale'] = 'nl-nl';
                    break;
                case 'nb':
                    $order_data['locale'] = 'nb-no';
                    break;
                case 'sv':
                    $order_data['locale'] = 'sv-se';
                    break;
                default:
                    $order_data['locale'] = 'en-gb';
            }
        }
    }


    private static function setGuiData(customer $customer, array &$order_data)
    {
        global $xtPlugin;

        $order_data['options']['radius_border'] = _KLARNA_CONFIG_KP_RADIUS_BORDER;
        $order_data['options']['color_border'] = _KLARNA_CONFIG_KP_COLOR_BORDER;
        $order_data['options']['color_border_selected'] = _KLARNA_CONFIG_KP_COLOR_BORDER_SELECTED;

        $order_data['options']['color_button'] = _KLARNA_CONFIG_KP_COLOR_BUTTON;
        $order_data['options']['color_button_text'] = _KLARNA_CONFIG_KP_COLOR_BUTTON_TEXT;
        $order_data['options']['color_checkbox'] = _KLARNA_CONFIG_KP_COLOR_CHECKBOX;
        $order_data['options']['color_checkbox_checkmark'] = _KLARNA_CONFIG_KP_COLOR_CHECKBOX_CHECKMARK;
        $order_data['options']['color_header'] = _KLARNA_CONFIG_KP_COLOR_HEADER;
        $order_data['options']['color_link'] = _KLARNA_CONFIG_KP_COLOR_LINK;

        $order_data['options']['color_details'] = _KLARNA_CONFIG_KP_COLOR_DETAILS;
        $order_data['options']['color_text'] = _KLARNA_CONFIG_KP_COLOR_TEXT;
        $order_data['options']['color_text_secondary'] = _KLARNA_CONFIG_KP_COLOR_TEXT_SECONDARY;
    }


    private static function setMerchantUrls($urls, &$order_data)
    {
        $debug = '';
        if(!empty($_SESSION['XDEBUG_SESSION_START']))
        {
            $debug = '&XDEBUG_SESSION_START='.$_SESSION['XDEBUG_SESSION_START'];

        }

        $merchantUrls = [
            //'confirmation'          =>  $urls['confirmation_shop'].'?event=success&kp_order_id={order.id}'.$debug,
            'push'                  =>  $urls['push'].'&event=push&kp_order_id={order.id}'.$debug,
            'notification'          =>  $urls['push'].'&event=notification&kp_order_id={order.id}'.$debug,
        ];
        $order_data['merchant_urls'] = $merchantUrls;
    }

    /**
     *    SETTLEMENTS API
     */

    /**  Payouts */
    public static function getPayoutSummary($start_date, $end_date, $currency_code = null)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $payout = new \Klarna\Rest\Settlements\Payouts($connector);

            $params = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'currency_code' => $currency_code
            ];
            $summary = $payout->getSummary($params);
            return $summary;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    public static function getPayout($payment_reference)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $payout = new \Klarna\Rest\Settlements\Payouts($connector);

            $payout->getPayout($payment_reference);
            return $payout;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    public static function getPayouts($offset = 0, $size = 0, $start_date = null, $end_date = null, $currency_code = null)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $payout = new \Klarna\Rest\Settlements\Payouts($connector);

            $params = [
                'offset' => $offset,
                'size' => $size,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'currency_code' => $currency_code
            ];
            $payouts = $payout->getAllPayouts($params);
            return $payouts;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }



    /**  Transactions */
    public static function getTransactions($payment_reference)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $transaction = new \Klarna\Rest\Settlements\Transactions($connector);

            $transaction->getTransactions($payment_reference);
            return $transaction;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    public static function getAllTransactions($offset = 0, $size = 0, $store_id)
    {
        try
        {
            $connector = self::getInstance()->getConnector($store_id);

            $transaction = new \Klarna\Rest\Settlements\Transactions($connector);

            $transaction->page($offset, $size);
            return $transaction;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }


    /**  Reports */
    public static function getPayoutReport($payment_reference)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $report = new \Klarna\Rest\Settlements\Reports($connector);

            $report->getPayoutWithTransactions($payment_reference);
            return $report;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    public static function getPayoutsSummaryReport($start_date, $end_date)
    {
        try
        {
            $connector = self::getInstance()->getConnector();

            $report = new \Klarna\Rest\Settlements\Reports($connector);

            $params = [
                'start_date' => $start_date,
                'end_date' => $end_date
            ];

            $stream = $report->getCSVPayoutsSummaryReport($params);
            $csv = $stream->getContents();
            return $csv;
        }
        catch(Exception $ex)
        {
            self::log(__FUNCTION__, func_get_args(), $ex);
            throw $ex;
        }
    }

    /**
     *    HELPER FUNCTIONS
     */


    public static function compareAddresses(array $a1, array $a2, $ignore = array())
    {

        foreach($a1 as $k => $v)
        {
            if (in_array($k, $ignore)) continue;
            if ($a1[$k] != $a2[$k]) return false;
        }
        return true;
    }


    public static function log($fnc, $xt_data = '', $kp_data = array(), Exception $ex = null)
    {
        $xt_data = '';

        if(!empty($ex) || (defined('_KP_LOG') && _KP_LOG === true))
        {
            $logFile = _SRV_WEBROOT . 'xtLogs/klarna_kp.log';
            if (file_exists($logFile) && filesize($logFile) > 10485760)
            {
                rename($logFile, _SRV_WEBROOT . 'xtLogs/klarna_kp.' . date('Y-m-d_H-i-s') . '.log');
            }
            $f = fopen(_SRV_WEBROOT . 'xtLogs/klarna_kp.log', 'a+');
            if ($f)
            {
                $s = date("[Y-m-d H:i:s]") . '  ' . $fnc . PHP_EOL;
                if (is_array($xt_data))
                {
                    $xt_data = print_r($xt_data, true);
                    $s .= 'xt data => ' . $xt_data;
                }
                else
                {
                    $s .= 'xt data => ' . $xt_data . PHP_EOL;
                }
                if(!empty($kp_data))
                {
                    $kp_data = print_r($kp_data, true);
                    $s .= 'kp data => ' . $kp_data;
                }

                if (!empty($ex))
                {
                    $s .= 'Exception => ' . $ex->getMessage() . PHP_EOL;
                    $s .= $ex->getTraceAsString() . PHP_EOL;
                }
                fwrite($f, $s);
                fclose($f);
            }
        }
    }

    public static function convert_country_code( $country, $iso3toIso2=false)
    {
        $countries = array(
            'AF' => 'AFG', //Afghanistan
            'AX' => 'ALA', //&#197;land Islands
            'AL' => 'ALB', //Albania
            'DZ' => 'DZA', //Algeria
            'AS' => 'ASM', //American Samoa
            'AD' => 'AND', //Andorra
            'AO' => 'AGO', //Angola
            'AI' => 'AIA', //Anguilla
            'AQ' => 'ATA', //Antarctica
            'AG' => 'ATG', //Antigua and Barbuda
            'AR' => 'ARG', //Argentina
            'AM' => 'ARM', //Armenia
            'AW' => 'ABW', //Aruba
            'AU' => 'AUS', //Australia
            'AT' => 'AUT', //Austria
            'AZ' => 'AZE', //Azerbaijan
            'BS' => 'BHS', //Bahamas
            'BH' => 'BHR', //Bahrain
            'BD' => 'BGD', //Bangladesh
            'BB' => 'BRB', //Barbados
            'BY' => 'BLR', //Belarus
            'BE' => 'BEL', //Belgium
            'BZ' => 'BLZ', //Belize
            'BJ' => 'BEN', //Benin
            'BM' => 'BMU', //Bermuda
            'BT' => 'BTN', //Bhutan
            'BO' => 'BOL', //Bolivia
            'BQ' => 'BES', //Bonaire, Saint Estatius and Saba
            'BA' => 'BIH', //Bosnia and Herzegovina
            'BW' => 'BWA', //Botswana
            'BV' => 'BVT', //Bouvet Islands
            'BR' => 'BRA', //Brazil
            'IO' => 'IOT', //British Indian Ocean Territory
            'BN' => 'BRN', //Brunei
            'BG' => 'BGR', //Bulgaria
            'BF' => 'BFA', //Burkina Faso
            'BI' => 'BDI', //Burundi
            'KH' => 'KHM', //Cambodia
            'CM' => 'CMR', //Cameroon
            'CA' => 'CAN', //Canada
            'CV' => 'CPV', //Cape Verde
            'KY' => 'CYM', //Cayman Islands
            'CF' => 'CAF', //Central African Republic
            'TD' => 'TCD', //Chad
            'CL' => 'CHL', //Chile
            'CN' => 'CHN', //China
            'CX' => 'CXR', //Christmas Island
            'CC' => 'CCK', //Cocos (Keeling) Islands
            'CO' => 'COL', //Colombia
            'KM' => 'COM', //Comoros
            'CG' => 'COG', //Congo
            'CD' => 'COD', //Congo, Democratic Republic of the
            'CK' => 'COK', //Cook Islands
            'CR' => 'CRI', //Costa Rica
            'CI' => 'CIV', //Côte d\'Ivoire
            'HR' => 'HRV', //Croatia
            'CU' => 'CUB', //Cuba
            'CW' => 'CUW', //Curaçao
            'CY' => 'CYP', //Cyprus
            'CZ' => 'CZE', //Czech Republic
            'DK' => 'DNK', //Denmark
            'DJ' => 'DJI', //Djibouti
            'DM' => 'DMA', //Dominica
            'DO' => 'DOM', //Dominican Republic
            'EC' => 'ECU', //Ecuador
            'EG' => 'EGY', //Egypt
            'SV' => 'SLV', //El Salvador
            'GQ' => 'GNQ', //Equatorial Guinea
            'ER' => 'ERI', //Eritrea
            'EE' => 'EST', //Estonia
            'ET' => 'ETH', //Ethiopia
            'FK' => 'FLK', //Falkland Islands
            'FO' => 'FRO', //Faroe Islands
            'FJ' => 'FIJ', //Fiji
            'FI' => 'FIN', //Finland
            'FR' => 'FRA', //France
            'GF' => 'GUF', //French Guiana
            'PF' => 'PYF', //French Polynesia
            'TF' => 'ATF', //French Southern Territories
            'GA' => 'GAB', //Gabon
            'GM' => 'GMB', //Gambia
            'GE' => 'GEO', //Georgia
            'DE' => 'DEU', //Germany
            'GH' => 'GHA', //Ghana
            'GI' => 'GIB', //Gibraltar
            'GR' => 'GRC', //Greece
            'GL' => 'GRL', //Greenland
            'GD' => 'GRD', //Grenada
            'GP' => 'GLP', //Guadeloupe
            'GU' => 'GUM', //Guam
            'GT' => 'GTM', //Guatemala
            'GG' => 'GGY', //Guernsey
            'GN' => 'GIN', //Guinea
            'GW' => 'GNB', //Guinea-Bissau
            'GY' => 'GUY', //Guyana
            'HT' => 'HTI', //Haiti
            'HM' => 'HMD', //Heard Island and McDonald Islands
            'VA' => 'VAT', //Holy See (Vatican City State)
            'HN' => 'HND', //Honduras
            'HK' => 'HKG', //Hong Kong
            'HU' => 'HUN', //Hungary
            'IS' => 'ISL', //Iceland
            'IN' => 'IND', //India
            'ID' => 'IDN', //Indonesia
            'IR' => 'IRN', //Iran
            'IQ' => 'IRQ', //Iraq
            'IE' => 'IRL', //Republic of Ireland
            'IM' => 'IMN', //Isle of Man
            'IL' => 'ISR', //Israel
            'IT' => 'ITA', //Italy
            'JM' => 'JAM', //Jamaica
            'JP' => 'JPN', //Japan
            'JE' => 'JEY', //Jersey
            'JO' => 'JOR', //Jordan
            'KZ' => 'KAZ', //Kazakhstan
            'KE' => 'KEN', //Kenya
            'KI' => 'KIR', //Kiribati
            'KP' => 'PRK', //Korea, Democratic People\'s Republic of
            'KR' => 'KOR', //Korea, Republic of (South)
            'KW' => 'KWT', //Kuwait
            'KG' => 'KGZ', //Kyrgyzstan
            'LA' => 'LAO', //Laos
            'LV' => 'LVA', //Latvia
            'LB' => 'LBN', //Lebanon
            'LS' => 'LSO', //Lesotho
            'LR' => 'LBR', //Liberia
            'LY' => 'LBY', //Libya
            'LI' => 'LIE', //Liechtenstein
            'LT' => 'LTU', //Lithuania
            'LU' => 'LUX', //Luxembourg
            'MO' => 'MAC', //Macao S.A.R., China
            'MK' => 'MKD', //Macedonia
            'MG' => 'MDG', //Madagascar
            'MW' => 'MWI', //Malawi
            'MY' => 'MYS', //Malaysia
            'MV' => 'MDV', //Maldives
            'ML' => 'MLI', //Mali
            'MT' => 'MLT', //Malta
            'MH' => 'MHL', //Marshall Islands
            'MQ' => 'MTQ', //Martinique
            'MR' => 'MRT', //Mauritania
            'MU' => 'MUS', //Mauritius
            'YT' => 'MYT', //Mayotte
            'MX' => 'MEX', //Mexico
            'FM' => 'FSM', //Micronesia
            'MD' => 'MDA', //Moldova
            'MC' => 'MCO', //Monaco
            'MN' => 'MNG', //Mongolia
            'ME' => 'MNE', //Montenegro
            'MS' => 'MSR', //Montserrat
            'MA' => 'MAR', //Morocco
            'MZ' => 'MOZ', //Mozambique
            'MM' => 'MMR', //Myanmar
            'NA' => 'NAM', //Namibia
            'NR' => 'NRU', //Nauru
            'NP' => 'NPL', //Nepal
            'NL' => 'NLD', //Netherlands
            'AN' => 'ANT', //Netherlands Antilles
            'NC' => 'NCL', //New Caledonia
            'NZ' => 'NZL', //New Zealand
            'NI' => 'NIC', //Nicaragua
            'NE' => 'NER', //Niger
            'NG' => 'NGA', //Nigeria
            'NU' => 'NIU', //Niue
            'NF' => 'NFK', //Norfolk Island
            'MP' => 'MNP', //Northern Mariana Islands
            'NO' => 'NOR', //Norway
            'OM' => 'OMN', //Oman
            'PK' => 'PAK', //Pakistan
            'PW' => 'PLW', //Palau
            'PS' => 'PSE', //Palestinian Territory
            'PA' => 'PAN', //Panama
            'PG' => 'PNG', //Papua New Guinea
            'PY' => 'PRY', //Paraguay
            'PE' => 'PER', //Peru
            'PH' => 'PHL', //Philippines
            'PN' => 'PCN', //Pitcairn
            'PL' => 'POL', //Poland
            'PT' => 'PRT', //Portugal
            'PR' => 'PRI', //Puerto Rico
            'QA' => 'QAT', //Qatar
            'RE' => 'REU', //Reunion
            'RO' => 'ROU', //Romania
            'RU' => 'RUS', //Russia
            'RW' => 'RWA', //Rwanda
            'BL' => 'BLM', //Saint Barth&eacute;lemy
            'SH' => 'SHN', //Saint Helena
            'KN' => 'KNA', //Saint Kitts and Nevis
            'LC' => 'LCA', //Saint Lucia
            'MF' => 'MAF', //Saint Martin (French part)
            'SX' => 'SXM', //Sint Maarten / Saint Matin (Dutch part)
            'PM' => 'SPM', //Saint Pierre and Miquelon
            'VC' => 'VCT', //Saint Vincent and the Grenadines
            'WS' => 'WSM', //Samoa
            'SM' => 'SMR', //San Marino
            'ST' => 'STP', //S&atilde;o Tom&eacute; and Pr&iacute;ncipe
            'SA' => 'SAU', //Saudi Arabia
            'SN' => 'SEN', //Senegal
            'RS' => 'SRB', //Serbia
            'SC' => 'SYC', //Seychelles
            'SL' => 'SLE', //Sierra Leone
            'SG' => 'SGP', //Singapore
            'SK' => 'SVK', //Slovakia
            'SI' => 'SVN', //Slovenia
            'SB' => 'SLB', //Solomon Islands
            'SO' => 'SOM', //Somalia
            'ZA' => 'ZAF', //South Africa
            'GS' => 'SGS', //South Georgia/Sandwich Islands
            'SS' => 'SSD', //South Sudan
            'ES' => 'ESP', //Spain
            'LK' => 'LKA', //Sri Lanka
            'SD' => 'SDN', //Sudan
            'SR' => 'SUR', //Suriname
            'SJ' => 'SJM', //Svalbard and Jan Mayen
            'SZ' => 'SWZ', //Swaziland
            'SE' => 'SWE', //Sweden
            'CH' => 'CHE', //Switzerland
            'SY' => 'SYR', //Syria
            'TW' => 'TWN', //Taiwan
            'TJ' => 'TJK', //Tajikistan
            'TZ' => 'TZA', //Tanzania
            'TH' => 'THA', //Thailand
            'TL' => 'TLS', //Timor-Leste
            'TG' => 'TGO', //Togo
            'TK' => 'TKL', //Tokelau
            'TO' => 'TON', //Tonga
            'TT' => 'TTO', //Trinidad and Tobago
            'TN' => 'TUN', //Tunisia
            'TR' => 'TUR', //Turkey
            'TM' => 'TKM', //Turkmenistan
            'TC' => 'TCA', //Turks and Caicos Islands
            'TV' => 'TUV', //Tuvalu
            'UG' => 'UGA', //Uganda
            'UA' => 'UKR', //Ukraine
            'AE' => 'ARE', //United Arab Emirates
            'GB' => 'GBR', //United Kingdom
            'US' => 'USA', //United States
            'UM' => 'UMI', //United States Minor Outlying Islands
            'UY' => 'URY', //Uruguay
            'UZ' => 'UZB', //Uzbekistan
            'VU' => 'VUT', //Vanuatu
            'VE' => 'VEN', //Venezuela
            'VN' => 'VNM', //Vietnam
            'VG' => 'VGB', //Virgin Islands, British
            'VI' => 'VIR', //Virgin Island, U.S.
            'WF' => 'WLF', //Wallis and Futuna
            'EH' => 'ESH', //Western Sahara
            'YE' => 'YEM', //Yemen
            'ZM' => 'ZMB', //Zambia
            'ZW' => 'ZWE', //Zimbabwe
        );

        if($iso3toIso2)
        {
            $countries = array_flip($countries);
        }
        $iso_code = isset( $countries[$country] ) ? $countries[$country] : $country;
        return $iso_code;
    }

}
