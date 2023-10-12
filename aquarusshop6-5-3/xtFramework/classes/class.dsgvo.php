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


class dsgvo
{
    const VERSION = '1.0';

    public static function sendEmailCustomerData($customers_id, $sendAdmin = false, $sendCustomer = true, customer $customer = null)
    {
        global $db;

        if(!$customer) $customer = new customer($customers_id);
        if(empty($customer->customer_info["shop_id"]))
        {
            throw new Exception('dsgvo (1) customer not available');
        }

        $s_xml = self::getXmlString(0, $customer);

        $mail = new xtMailer('dsgvo',
            $customer->customer_info["customers_default_language"],
            $customer->customer_info["customers_status"],
            '0',
            $customer->customer_info["shop_id"]);

        $mail->_assign('customers_title', $customer->customer_default_address["customers_title"]);
        $mail->_assign('customers_firstname', $customer->customer_default_address["customers_firstname"]);
        $mail->_assign('customers_lastname', $customer->customer_default_address["customers_lastname"]);

        $attName = 'dsgvo_data_'.$customer->customers_id.'_'.date('Y-m-d_H-i-s').'_.xml';
        $mail->addStringAttachment($s_xml, $attName);


        $store_name     = $db->GetOne('SELECT language_value FROM '.TABLE_CONFIGURATION_LANG_MULTI." WHERE config_key = '_STORE_NAME' AND store_id=?", array($customer->customer_info["shop_id"]));
        $store_contact  = $db->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION_MULTI.$customer->customer_info["shop_id"]." WHERE config_key = '_STORE_CONTACT_EMAIL'");

        $mail->_setFrom($store_contact, $store_name);

        if ($sendCustomer)
        {
            $mail->_addReceiver($customer->customer_info["customers_email_address"],
                $customer->customer_default_address["customers_firstname"].' '.$customer->customer_default_address["customers_lastname"]);
        }
        if ($sendAdmin)
        {
            $admin_email_address = !empty($store_contact) ? $store_contact : _CORE_DEBUG_MAIL_ADDRESS;
            $mail->_addBCC($admin_email_address);
        }
        $mail->_sendMail($store_contact, $store_name);
    }

    public static function getXmlString($customers_id = 0, customer $customer = null)
    {
        $xml = self::getSimpleXml($customers_id, $customer);
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $s_xml = $dom->saveXML();

        return $s_xml;
    }

    private static function getSimpleXml($customers_id = 0, customer $customer = null)
    {
        global $xtPlugin;

        $data = array();

        if(!$customer) $customer = new customer($customers_id);
        if(empty($customer->customer_info["shop_id"]))
        {
            throw new Exception('dsgvo (2) customer not available');
        }
        $customers_id = $customer->customers_id;

        $data['customer'] = (array) $customer;

        $addresses = self::getAddresses($customers_id);
        $data['addresses'] = (array) $addresses;

        $orders = self::getOrders($customers_id);
        $data['orders'] = (array) $orders;

        ($plugin_code = $xtPlugin->PluginCode('class.dsgvo:collect_data')) ? eval($plugin_code) : false;

        $xml = new SimpleXMLExtended("<data></data>");
        $xml->addAttribute('created', date(DATE_ATOM));
        $xml->addAttribute('creator', 'xtCommerce/'._SYSTEM_VERSION.' dsgvo/'.self::VERSION);
        self::to_xml($data, $xml, array_unique(self::$skip));

        return $xml;
    }

    private static function getAddresses($customers_id)
    {
        global $db;

        $rs = $db->GetArray('SELECT * FROM '.TABLE_CUSTOMERS_ADDRESSES.' WHERE customers_id=?', $customers_id);

        return $rs;
    }

    private static function getOrders($customers_id)
    {
        global $db;

        $data = array();

        $rs = $db->GetArray('SELECT orders_id FROM '.TABLE_ORDERS.' WHERE customers_id=?', $customers_id);
        foreach($rs as $o)
        {
            $data[] = (array) new order($o['orders_id'], $customers_id);
        }
        return $data;
    }

    static function to_xml($array, SimpleXMLExtended &$xml, $skip = array(), $path = '')
    {
        global $xtPlugin;



        if(is_object($array))
        {
            $array = json_decode(json_encode($array),true);
        }

        $lokalPath = $path;
        foreach($array as $key => $value)
        {
            if(in_array($key, $skip) && $key!==0) {
                continue;
            }

            $path = $lokalPath.'.'.$key;
            //error_log($path);
            $skip_path = false;
            foreach (self::$skip_pattern as $p)
            {
                if (preg_match("/" . $p . "/", $path))
                {
                    $skip_path = true;
                    //error_log($p.'   '.$path);
                    break;
                }
            }
            if($skip_path) continue;

            if(is_numeric($key)) {
                $path_parts = explode('.', $path);
                $parent = $path_parts[count($path_parts)-2];
                $key = !empty($parent) ? "{$parent}_{$key}" :  "item_{$key}";
            }
            if(is_array($value) && count($value))
            {
                $subnode = $xml->addChild("$key");

                ($plugin_code = $xtPlugin->PluginCode('class.dsgvo:add_subnode')) ? eval($plugin_code) : false;

                self::to_xml($value, $subnode, $skip, $path);
            }
            else if(!empty($value))
            {
                static $purifier;
                if(empty($purifier))
                {
                    $config = HTMLPurifier_Config::createDefault();
                    $config->set('HTML.Allowed', '');
                    $config->set('Cache.SerializerPath', _SRV_WEBROOT . 'templates_c');
                    $purifier = new HTMLPurifier($config);
                }
                $value = trim($value);
                if(strpos($value,'<') ===0)
                {
                    //$value = $purifier->purify($value);
                }

                ($plugin_code = $xtPlugin->PluginCode('class.dsgvo:add_child')) ? eval($plugin_code) : false;
                $xml->addChildWithCData("$key",$value);
            }
        }
    }

    private static $skip_pattern = array(
        'tax.data',
        'price.data',
        'tax_value.data',
        'data_total.data',
        'product_total.data',
        'total.data',
        'order_products.[\d]+.orders_products_id',
        'order_products.[\d]+.products_data',
        'order_products.[\d]+.products_info_data',
        'order_products.[\d]+.products_info_options',
        'products_price.data',
        'add_single_tax.data',
        'orders_total_tax.data',
        'product_tax_[\d]+.data',

    );
    private static $skip = array(
        '_master_key',
        '_master_key_add',
        '_table',
        '_table_add',
        'customers_id',
        'products_id',
        'orders_products_id',
        'password_special_signs',
        'master_id',
        'shop_id',
        'products_description',
        'products_short_description',
        'address_book_id',
        'formated',
    );

}

class SimpleXMLExtended extends SimpleXMLElement
{
    public function addChildWithCData($name, $value = NULL) {
        $new_child = $this->addChild($name);

        $node = dom_import_simplexml($new_child);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($value));

        return $new_child;
    }
}
