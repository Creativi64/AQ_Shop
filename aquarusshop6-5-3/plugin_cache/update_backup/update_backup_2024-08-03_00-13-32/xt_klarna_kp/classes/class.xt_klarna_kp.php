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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/class.klarna_kp.php';


class xt_klarna_kp extends klarna_kp
{
    var $data = array();

    private $_master_key = 'COL_KCD__ID_PK';
    private $_table = 'TABLE_KCD_';
    private $_table_lang = '';
    private $_table_seo = '';
    private $_sql_limit = 50;

    // supported Payment Method Categories
    public $subpayments = true;
    public $allowed_subpayments = ["pay_later", "pay_now", "pay_over_time"];

    const _nodeUrl = 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=getKlarnaDetails&';
    const _updateUrl = 'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=getKlarnaDetails&';
    const _icons_path = 'images/icons/';

    public $position = '';
    public $url_data = array();

    const address_key_mappings = array(
        'customers_country_code' => 'country',
        'customers_city' => 'city',
        'customers_postcode' => 'postal_code',
        'customers_firstname' => 'given_name',
        'customers_lastname' => 'family_name',
        'email' => 'email',
        'customers_gender' => 'title',
        'customers_street_address' => 'street_address',
        'customers_address_addition' => 'street_address2',
        'customers_suburb' => 'region',
        'customers_mobile_phone' => 'phone',
        'customers_phone' => 'phone'
    );

    const gender_salutation_mappings = array(
        'f' => array('Ms', 'Mrs', 'Miss', 'Frau', 'Mevr.'),
        'm' => array('Mr', 'Herr'),
        'c' => array()
    );

    public static function getCallbackUrls()
    {
        global $xtLink, $_content;

        $content_array_agb = $_content->getHookContent(3);
        $content_array_rsc = $_content->getHookContent(8);

        return array(
            'push' => $xtLink->_link(array('page' => 'callback', 'paction' => 'xt_klarna_kp', 'conn' => 'SSL')),
            'confirmation_klarna' => $xtLink->_link(array('page' => 'checkout', 'paction' => 'process', 'conn' => 'SSL')),
            'confirmation_shop' => $xtLink->_link(array('page' => 'checkout', 'paction' => 'success', 'conn' => 'SSL')),
            'terms' => $content_array_agb["content_link"],
            'cancellation_terms' => $content_array_rsc["content_link"]
        );

    }

    public function getPaymentsSession(cart $cart, customer $customer, $reset = true)
    {
        global $customers_status, $language, $currency, $info, $store_handler;

        $kp_session = $_SESSION['kp_session'] = $reset ? null : $_SESSION['kp_session'];
        try
        {
            if (is_array($cart->show_content) && count($cart->show_content))
            {
                $errors = array();

                if (empty($kp_session))
                {
                    $urls = xt_klarna_kp::getCallbackUrls();

                    $b2b_groups = $this->get_saved_klarna_kp_b2b_groups(['store_id' => $store_handler->shop_id], true);
                    $b2b = $_SESSION['kp_session_b2b'] = in_array($customers_status->customers_status_id, $b2b_groups);

                    $kp_session = klarna_kp::createPaymentsSession($cart, $customer, $customers_status, $urls, $b2b, $errors);
                    $kp_session = $kp_session->getArrayCopy();
                }
                else {
                    $kp_session = klarna_kp::updatePaymentsSession($kp_session["session_id"], $cart, $customer, $customers_status, $errors);
                    $kp_session = $kp_session->getArrayCopy();
                }

                if (!empty($_SESSION['kp_session']) && $_SESSION['kp_session_lng'] != $language->code)
                {
                        $sid = $_SESSION['kp_session']['session_id'];
                        $kp_session = klarna_kp::setSessionLanguage($language->code, $_SESSION['kp_session']['session_id']);
                        $kp_session = $kp_session->getArrayCopy();
                        $kp_session['session_id'] = $sid;
                }

                $_SESSION['kp_session_lng'] = $language->code;

                if (count($errors))
                {
                    foreach ($errors as $error)
                    {
                        $msg = TEXT_KLARNA_GENERAL_ERROR;
                        $details = $error['key'] . ' ';
                        echo '<div class="alert alert-danger klarna-error" role="alert"><p class="item"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;' . $msg . '</p><p style="font-size:0.8em">' . $details . '</p></div>';
                    }
                }
            }
        }
        catch (KlarnaApiException $e)
        {
            unset($_SESSION['kp_session']);

            if (strpos($e->getMessage(), 'Bad value: purchase_country') !== false)
            {
                error_log($e->getMessage());
                //$html = '<div><p>' . sprintf(WARNING_NO_KLARNA_COUNTRY, strtoupper($e->getData()['purchase_country'])) . '</p><p style="font-size:0.8em">' . $e->getMessage() . '</p></div>';
                //$info->_addInfo($html, 'warning');
            }
            else
            {
                error_log($e->getMessage());
                //$html = '<div><p>' . TEXT_KLARNA_GENERAL_ERROR . '</p><p style="font-size:0.8em">' . $e->getMessage() . '</p></div>';
                //$info->_addInfo($html, 'error');
            }
        }
        catch (Exception $e)
        {
            unset($_SESSION['kp_session']);

            // check if error because no SSL is activated
            if (strpos($e->getMessage(), 'must be a valid https URI') !== false)
            {
                error_log($e->getMessage());
                echo '<img src="plugins/xt_klarna_kp/images/kp_no_ssl_desktop.png" style="opacity: 0.5"/>';

            }
            else
            {
                error_log($e->getMessage());
                $html = '<div><p>' . TEXT_KLARNA_GENERAL_ERROR . '</p><p style="font-size:0.8em">' . $e->getMessage() . '</p></div>';
                $info->_addInfo($html, 'error');
            }
        }
        return $kp_session;
    }

    function __construct()
    {
    }

    function setPosition($position)
    {
        $this->position = $position;
    }

    function _getParams()
    {
        $header = array();
        //$header[COL_KCO__ID_PK] = array('type' => 'textfield', 'readonly'=>true);

        /** HEADER KEYS, see class.ExtFunctions.php for more
         *
         * $header[SOME_HEADER_KEY] = array(
         * 'type' => 'textarea',
         * 'readonly'=>false,
         * 'value'=>'defVal',
         * 'text'=>'field_title',
         * 'width'=> '400px',
         * 'height' => '30px',
         * 'required' => true,
         * 'min' => 8, // min length
         * 'max' => 50 // max length        );
         */

        /** TYPES, see class.ExtFunctions.php
         *
         * default type => textfield
         * weiter ermittlung mit preg_match(header_key)
         *      desc,textarea => textarea
         *      price => price
         *      status && !adminActionStatus => status
         *      adminActionStatus => adminActionStatus
         *      shop,flag,permission,fsk18,startpage => status
         *      date,last_modified => date
         *      image => image
         *      file => file
         *      icon => icon
         *      url => url
         *      _html => htmleditor
         *      template => dropdown
         *      _permission_info => admininfo
         *      hidden => hidden
         */

        /** TYPE dropdown
         *
         * $header['some_dropdown_field'] = array(
         * 'type' => 'dropdown',
         * 'url'  => 'DropdownData.php?get=some_dropdown_data','text'=>TEXT_SOME_DROPDOWN_FIELD);
         */
        /** TODO add hook admin_dropdown.php:dropdown, eg
         *
         * case 'some_dropdown_data':
         * $data=array();
         *
         * $data[] =  array(
         * 'id' => 'option_1',
         * 'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_1);
         * $data[] =  array('
         * id' => 'option_2',
         * 'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_2);
         * $data[] =  array(
         * 'id' => 'option_3',
         * 'name' => TEXT_SOME_DROPDOWN_FIELD_OPTION_3);
         * $result=$data;
         *
         * break;
         */

        $params = array();
        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['display_deleteBtn'] = true;
        $params['display_resetBtn'] = true;
        $params['display_editBtn'] = true;
        $params['display_newBtn'] = true;
        $params['display_searchPanel'] = false;
        $params['display_statusTrueBtn'] = false;
        $params['display_statusFalseBtn'] = false;

        $rowActionsFunctions = array();
        $rowActions = array();
        /** ROW ACTION JS
         *
         * $url_backend = _SRV_WEB. "adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=xt_klarna_kp_row_fnc_1&row_fnc_1_edit_id=";
         * $js_backend  = "
         * var edit_id = record.data.id;
         * addTab('".$url_backend."' + edit_id,'".TEXT_xt_klarna_kp_ROW_FNC_1." ' + record.data.id);
         * ";
         * $rowActionsFunctions['xt_klarna_kp_row_fnc_1'] = $js_backend;
         * $rowActions[] = array('iconCls' => 'xt_klarna_kp_row_fnc_1', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_XT_KLARNA_KP_ROW_FNC_1);
         */

        if (count($rowActionsFunctions) > 0)
        {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }

        if (count($rowActions) > 0)
        {
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }


        return $params;
    }

    public function xt_klarna_kp_row_fnc_1($data)
    {
        return 'result of xt_klarna_kp_row_fnc_1<br />data:<br />' . print_r($data, true);
    }

    public function _get($ID = 0)
    {
        if ($this->position != 'admin')
        {
            return false;
        }

        if (!$ID && !isset($this->sql_limit))
        {
            $this->sql_limit = "0,25";
        }

        $table_data = new adminDB_DataRead(
            $this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', $this->_sql_limit,
            /*permission*/
            '', /*filter*/
            '', /*sort*/
            '');

        $data = array();

        if ($this->url_data['get_data'])
        {
            $data = $table_data->getData();

            /** für die view können hier werte angepasst/geändert werden */
            /*
            if (is_array($data) && sizeof($data)>0)
            {
                for($i=0; $i<sizeof($data); $i++)
                {
                    $changedVal = 'changedVal';
                    $data[$i]['SOME_KEY'] = $changedVal ;
                }
            }
            */
        }
        else
        {
            if ($ID === 'new')
            {

            }
            elseif ($ID)
            {
                $data = $table_data->getData($ID);
            }
            else
            {
                $data = $table_data->getHeader();
            }
        }

        if (!$this->url_data['get_data'])
        {
            // rebuilding fields' order
            $defaultOrder = array(//COL_KCD__ID_PK,
            );

            $orderedData = array();
            foreach ($defaultOrder as $key)
            {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);
        }

        $obj = new stdClass;
        if ($table_data->_total_count != 0 || !$table_data->_total_count)
        {
            $count_data = $table_data->_total_count;
        }
        else
        {
            $count_data = count($data);
        }
        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
    }


    function _set($data, $set_type = 'edit')
    {
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $result = $o->saveDataSet();

        return $result;
    }


    function _unset($id = 0)
    {
        global $db;

        $delete = "DELETE FROM `" . $this->_table . "` WHERE `" . $this->_master_key . "`= '$id'";
        $db->Execute($delete);
        $affectedRows = $db->Affected_Rows();

        return $affectedRows >= 1;
    }

    function get_saved_klarna_kp_b2b_groups($data, $raw = false)
    {
        global $db, $customers_status;

        $store_id = $data["store_id"];
        $obj = new stdClass();
        $obj->topics = array();
        $obj->totalCount = 0;

        $group_ids = $db->GetOne("SELECT  config_value FROM " . DB_PREFIX . "_config_" . $store_id . " WHERE config_key='_KLARNA_CONFIG_KP_B2B_GROUPS'");

        if ($group_ids)
        {

            $groups_array = explode(',', $group_ids);

            if($raw) return $groups_array;

            foreach ($groups_array as $group_id) {
                $obj->topics[] = array('id' => $group_id, 'name' => $customers_status->getGroupName($group_id), 'desc' => '');

            }
        }
        elseif ($raw) return [];
        $obj->totalCount = count($obj->topics);
        return json_encode($obj);
    }

    function get_saved_klarna_kp_payment_methods($data, $raw = false)
    {
        global $db;

        $store_id = $data["store_id"];
        $obj = new stdClass();
        $obj->topics = array();
        $obj->totalCount = 0;

        $list = $db->GetOne("SELECT  config_value FROM " . DB_PREFIX . "_config_" . $store_id . " WHERE config_key='_KLARNA_CONFIG_KP_PAYMENT_METHODS'");

        if ($list)
        {

            $pms = explode(',', $list);

            if($raw) return $pms;

            foreach ($pms as $pm) {
                $obj->topics[] = array('id' => $pm, 'name' => __define('TEXT_KLARNA_KP_'.$pm), 'desc' => '', __define('TEXT_KLARNA_KP_'.$pm.'_DESC'));

            }
        }
        $obj->totalCount = count($obj->topics);
        return json_encode($obj);
    }


    function get_saved_klarna_kp_triggers($data, $raw = false)
    {
        global $db;

        $store_id = $data["store_id"];
        $key      = $data["key"];
        $obj = new stdClass();
        $obj->topics = array();
        $obj->totalCount = 0;

        $list = $db->GetOne("SELECT  config_value FROM " . DB_PREFIX . "_config_" . $store_id . " WHERE config_key=?", [$key]);

        if ($list)
        {

            $pms = explode(',', $list);

            if($raw) return $pms;

            require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.system_status.php';
            $s_status = new system_status();

            $_data = $s_status->values['order_status'];

            foreach ($pms as $pm) {
                $obj->topics[] = array('id' => $_data[$pm]['id'], 'name' => $_data[$pm]['name'], 'desc' => '', 'name' => $_data[$pm]['name']);

            }
        }
        $obj->totalCount = count($obj->topics);
        return json_encode($obj);
    }


    /**************************************************************************
     *
     *  Backend functions
     *
     */

    function updateOrder($data)
    {
        global $xtPlugin;

        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->msg = 'OK';

        try
        {
            $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($data['orders_id']);
            $kp_order_id = $kp_order_arr['order_id'];

            if($kp_order_id)
            {
                $oId = (int) $data['orders_id'];
                if ($oId)
                {
                    $order = new order($oId, -1);
                    $order_amount = $order_tax_amount = 0;
                    $order_lines = array();
                    self::buildOrderLines($order,$order_lines, $order_amount, $order_tax_amount);

                    klarna_kp::setNewOrderAmount($kp_order_id, round($order->order_total["total"]["plain"]*100,0), $order_lines);
                }
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

    static function updateOrderAddress($data)
    {
        global $xtPlugin;

        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->msg = 'OK';

        try
        {
            $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($data['orders_id']);
            $kp_order_id = $kp_order_arr['order_id'];

            if($kp_order_id)
            {
                $oId = (int) $data['orders_id'];
                if ($oId)
                {
                    $order = new order($oId, -1);
                    $order_amount = $order_tax_amount = 0;
                    $order_lines = array();
                    self::buildOrderLines($order,$order_lines, $order_amount, $order_tax_amount);

                    klarna_kp::setNewOrderAmount($kp_order_id, round($order->order_total["total"]["plain"]*100,0), $order_lines);
                }
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

    static function buildOrderLines(order $order, &$order_lines, &$order_amount, &$order_tax_amount)
    {
        global $db, $xtLink, $system_status;

        foreach ($order->order_products as $sc)
        {
            $total_amount       = round($sc['products_final_price']['plain'] * 100,0);
            $total_tax_amount   = round($sc['products_final_tax']['plain'] * 100,0);

            $p_data = $db->GetArray('SELECT products_ean, products_mpn, manufacturers_id, products_image, products_unit FROM '.TABLE_PRODUCTS. " WHERE products_id=?", array( $sc['products_id'] ));
            if(isset($p_data[0]))
            {
                $p_ean =  $p_data[0]['products_ean'];
                $p_mpn =  $p_data[0]['products_mpn'];
                $p_man =  $p_data[0]['manufacturers_id'];
                $p_img =  $p_data[0]['products_image'];
                $p_unit = $p_data[0]['products_unit'];
                if(!empty($p_unit))
                {
                    $a = $system_status->getSingleValue($p_unit, $order->order_data["language_code"]);
                    $sc['products_unit_name'] = isset($a["status_name"]) ? $a["status_name"] : null;
                }
            }

            $unit_price = round($sc['products_price']['plain'] * 100,0);

            $total_discount_amount = 0;

            $sc["products_discount"] = (float) $sc["products_discount"];
            if($sc["products_discount"] > 0)
            {
                $q = 1 - $sc["products_discount"] / 100;
                $unit_price = round($unit_price / $q, 0);

                $total_amount_without_discount = $total_amount/$q;

                $total_discount_amount = round( $total_amount_without_discount - $total_amount, 0);
            }

            $product_identifiers = null;
            $identifiers = array();
            if(!empty($p_ean))
            {
                $identifiers["global_trade_item_number"] = preg_replace('/[^a-zA-Z0-9]/', '' , $p_ean);
            }
            if(!empty($p_mpn))
            {
                $identifiers["manufacturer_part_number"] = preg_replace('/[^a-zA-Z0-9]/', '' , $p_mpn);
            }
            if(count($identifiers))
            {
                $product_identifiers = $identifiers;
            }


            $order_lines[] = array(
                "type"          => $sc['products_digital'] == 0 ? "physical" : "digital",
                "reference"     => substr($sc['products_model'], 0 , 64),
                "name"          => $sc['products_name'],
                "quantity"      => (float) $sc['products_quantity'],
                "quantity_unit" => $sc['products_unit_name'],
                "unit_price"    => $unit_price,
                "tax_rate"      => round($sc['products_tax_rate'] * 100,0),
                "total_amount"  => (int) $total_amount,
                "total_tax_amount" => (int) $total_tax_amount,
                "total_discount_amount" => $total_discount_amount,
                "product_identifiers" => $product_identifiers
            );

            if (_KLARNA_CONFIG_SEND_EXTENDED_PRODUCT_INFO == 1)
            {
                $link_array = array('page'=> 'product', 'type'=>'product', 'id'=>$sc['products_id']);
                $products_link = $xtLink->_link($link_array, '/xtAdmin');
                $order_lines[count($order_lines)-1]['product_url'] = $products_link;

                if(!empty($p_img))
                {
                    $params = array('img' =>'product:'.$p_img, 'type' => 'm_info', 'path_only' => true, 'return' => true);
                    $img_url = image::getImgUrlOrTag($params, new stdClass());
                    if(!empty($img_url))
                    {
                        $order_lines[count($order_lines) - 1]['image_url'] = $img_url;
                    }
                }
            }

            if(!empty($p_man))
            {
                global $manufacturer;
                $man_data = $manufacturer->getManufacturerData($p_man);
                if(!empty($man_data['manufacturers_name']))
                {
                    $order_lines[count($order_lines)-1]['product_identifiers']['brand'] = $man_data['manufacturers_name'];
                }
            }

            $order_amount       += $total_amount;
            $order_tax_amount   += $total_tax_amount;
        }

        foreach ($order->order_total_data as $k => $sc)
        {
            global $db;

            if(in_array($sc['orders_total_key'], array(/*'shipping',*/ 'payment'))) continue;

            $total_amount       = round($sc["orders_total_final_price"]["plain"] * 100,0);
            $total_tax_amount   = round($sc["orders_total_final_tax"]["plain"] * 100,0);

            $type = null;
            switch($sc['orders_total_key'])
            {
                case 'xt_coupon':
                    $type = 'discount';
                    break;
                case 'shipping':
                    $type = 'shipping_fee';

                    $l_key = USER_POSITION == 'store' ? 'TEXT_SHIPPING_COSTS' : 'TEXT_SHIPPING';

                    $txt = $db->GetOne("SELECT language_value FROM ".TABLE_LANGUAGE_CONTENT. " WHERE language_key=? AND language_code=?",
                        array($l_key, $order->order_data["language_code"]));
                    $sc['orders_total_name'] = $txt ? $txt : 'shipping';

                    break;
                default:
                    // TODO insert hookpoint
            }

            $order_lines[] = array(
                "type"          => $type,
                "reference"     => $sc['orders_total_model'],
                "name"          => $sc['orders_total_name'],
                "quantity"      => 1,
                "quantity_unit" => null,
                "unit_price"    => round($sc["orders_total_price"]["plain"] * 100,0),
                "tax_rate"      => round($sc["orders_total_tax_rate"] * 100,0),
                "total_amount"  => $total_amount,
                "total_tax_amount" => $total_tax_amount
            );
            $order_amount       += $total_amount;
            $order_tax_amount   += $total_tax_amount;
        }
    }

    function autoCapture($xt_orders_id)
    {
        global $xtPlugin;
        $data['orders_id'] = $xt_orders_id;
        $data['auto_capture'] = true;

        if (isset($xtPlugin->active_modules['xt_ship_and_track']))
        {
            require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_tracking.php';
            $trackings = xt_tracking::getTrackingForOrder($xt_orders_id);
            if(count($trackings))
            {
                $data['capture_tracking'] = array();
                foreach($trackings as $t)
                {
                    $data['capture_tracking'][$t['id']] = true;
                }
            }

        }
        return $this->capture($data);
    }

    function capture($data)
    {
        global $xtPlugin;

        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->msg = 'OK';

        try
        {
            $kp_order = xt_klarna_kp::getKlarnaOrderFromXt($data['orders_id']);
            $kp_order_id = $kp_order['order_id'];

            if($kp_order_id)
            {
                if($kp_order['fraud_status'] != 'ACCEPTED')
                {
                    throw new Exception(TEXT_KLARNA_CANT_CAPTURE_FRAUD_STATUS);
                }

                if (empty($data['capture_amount']))
                {
                    $amount = $kp_order['remaining_authorized_amount'];
                    $description = '';
                }
                else
                {
                    $amount = floatval(str_replace(',', '.', $data['capture_amount'])) * 100;
                    $description = $data['capture_description'];
                }

                if ($amount > 0)
                {
                    $shipping_info = array();
                    if (isset($xtPlugin->active_modules['xt_ship_and_track']))
                    {
                        if (is_array($data['capture_tracking']) && count($data['capture_tracking']))
                        {
                            $shipping_info = $this->buildShippingInfo(array_keys($data['capture_tracking']));
                        }
                    }

                    $order_lines = array();
                    if(is_array($kp_order))
                    {
                        foreach($kp_order['order_lines'] as $k => $ol)
                        {
                            $key = $ol['type'].'#'.$ol['reference'];
                            if(in_array($key, $data["order_line"]) || $data['auto_capture'])
                            {
                                $order_lines[] = $ol;
                            }
                        }
                    }

                    $kp = klarna_kp::getInstance()
                        ->setXtorderId($data['orders_id']);
                    $kp->createCapture($kp_order_id , $amount, $description, $shipping_info, $order_lines);
                    if ($data['release'] == true || $data['release'] == '1')
                    {
                        $kp->doRelease($kp_order_id, $data['orders_id']);
                    }
                    $kp_order = $kp->getOrder($kp_order_id);
                    xt_klarna_kp::setKlarnaOrderInXt($kp_order, $data['orders_id']);
                }
                else {
                    throw new Exception(TEXT_KLARNA_CANT_CAPTURE_AMOUNT_ZERO);
                }
            }
        }
        catch (Exception $e)
        {
            if($data['auto_capture']) throw $e;

            error_log($e->getMessage());
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . (!empty($msg[1]) ? '::' . $msg[1] : '');
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

    function refund($data)
    {
        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->msg = 'OK';

        try
        {
            $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($data['orders_id']);
            $kp_order_id = $kp_order_arr['order_id'];
            $amount = round(floatval(str_replace(',','.', $data['refund_amount'])) * 100, 0);
            $description = trim($data['refund_description']);

            $kp_order = klarna_kp::getKlarnaOrderFromXt($data["orders_id"]);
            $order_lines = array();
            if(is_array($kp_order))
            {
                foreach($kp_order['order_lines'] as $k => $ol)
                {
                    $key = $ol['type'].'#'.$ol['reference'];
                    if(in_array($key, $data["order_line"]))
                    {
                        $order_lines[] = $ol;
                    }
                }
            }

            $kp = klarna_kp::getInstance()
                ->setXtorderId( $data['orders_id']);
            $kp->doRefund($kp_order_id, $amount, $description, $order_lines);
            $kp_order = $kp->getOrder($kp_order_id);
            xt_klarna_kp::setKlarnaOrderInXt($kp_order, $data['orders_id']);
        }
        catch (Exception $e)
        {
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }


    function cancel($data)
    {
        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->msg = 'OK';

        try
        {
            $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($data['orders_id']);
            $kp_order_id = $kp_order_arr['order_id'];
            $kp = klarna_kp::getInstance()
                ->setXtorderId( $data['orders_id']);
            $kp->cancelOrder($kp_order_id);
            $kp_order = $kp->getOrder($kp_order_id);
            xt_klarna_kp::setKlarnaOrderInXt($kp_order, $data['orders_id']);
        }
        catch (Exception $e)
        {
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

    function release($data)
    {
        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->msg = 'OK';

        try
        {
            $kp_order_arr = xt_klarna_kp::getKlarnaOrderFromXt($data['orders_id']);
            $kp_order_id = $kp_order_arr['order_id'];
            $kp = klarna_kp::getInstance()
                ->setXtorderId( $data['orders_id']);
            $kp->doRelease($kp_order_id);
            $kp_order = $kp->getOrder($kp_order_id);
            xt_klarna_kp::setKlarnaOrderInXt($kp_order, $data['orders_id']);
        }
        catch (Exception $e)
        {
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

    function triggerResend($data)
    {
        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->msg = 'OK';

        try
        {
            xt_klarna_kp::triggerResendCustomerCommunication($data['kp_order_id'], $data['capture_id']);
        }
        catch (Exception $e)
        {
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

    function extendAuth($data)
    {
        $r = new stdClass();
        $r->success = true;
        $r->refresh = true;
        $r->payload = new stdClass();
        $r->msg = 'OK';

        try
        {
            klarna_kp::extendAuthorizationTime($data['kp_order_id']);
            $order = klarna_kp::getOrder($data['kp_order_id']);
            $r->payload->expires_at = $order['expires_at'];
            $r->payload->kp_ref = $order['expires_at'];
        }
        catch (Exception $e)
        {
            $r->success = false;
            $r->refresh = false;
            $msg = explode('|', $e->getMessage());
            $msg[0] = $msg[0] . '::' . $msg[1];
            unset($msg[1]);
            $r->msg = implode("<br/>", $msg);
        }

        return json_encode($r);
    }

    public static function getKlarnaConfig($store_id)
    {
        global $db;

        $klarna_config = array();

        if($store_id)
        {
            $klarna_config_db = $db->GetArray('SELECT config_key, config_value FROM ' . TABLE_CONFIGURATION_MULTI . $store_id . ' WHERE group_id = 101 ');

            if (is_array($klarna_config_db))
            {
                foreach ($klarna_config_db as $kc_entry)
                {
                    $klarna_config[$kc_entry['config_key']] = $kc_entry['config_value'];
                }
            }
        }

        return $klarna_config;
    }


    /**************************************************************************
     *
     *  Frontend functions
     *
     */

    /**
     * check if customer is logged in already
     * if not login customer
     * if customer is new, create account first
     *
     * @param $kp_order
     * @return bool  true if existing customer, else false
     */
    function checkLoginCustomer($kp_order)
    {
        global $db, $store_handler, $xtPlugin;

        if (!isset ($_SESSION['registered_customer']))
        {
            $sql = "SELECT customers_id FROM " . TABLE_CUSTOMERS . " where customers_email_address = ? and account_type = '0'";
            $sql_ar = array($kp_order['billing_address']['email']);
            $check_shop_id = true;
            ($plugin_code = $xtPlugin->PluginCode('class.xt_klarna_kp.php:checkCustomer_check_shop_id')) ? eval($plugin_code) : false;
            if($check_shop_id)
            {
                $sql .= " AND shop_id=? ";
                $sql_ar[] = $store_handler->shop_id;
            }

            $record = $db->Execute($sql, $sql_ar);

            if($record->RecordCount() == 0)
            {
                $cust_id = $this->createAccount($kp_order);
                $this->loginCustomer($cust_id);

                if($kp_order['merchant_requested']['additional_checkbox']==true)
                {
                    $customer = sessionCustomer();
                    $customer->customerAdressData['default']['customers_lastname'] = $kp_order["billing_address"]["family_name"];
                    $customer->customerAdressData['default']['customers_firstname'] = $kp_order["billing_address"]["given_name"];
                    $customer->_sendAccountMail();
                    $customer->_sendNewPassword();
                }

                return false;
            }
            else
            {
                //$this->loginCustomer($record->fields['customers_id']);
                return true;
            }
        }
    }


    /**
     * check if given address data found as address in xt
     *
     * @return
     */
    function checkAddress(array $address_data, customer $customer)
    {
        $addresses = $customer->_getAdressList($customer->customers_id);

        if(is_array($addresses))
        {
            foreach($addresses as $k => $xt_address)
            {
                $same = self::compareAddresses($xt_address, $address_data);
                if($same) return $xt_address;
            }
        }

        return $address_data;
    }

    function checkAddAddress(array $klarna_address_data, customer $customer, $dob, $addType = 'default')
    {
        $klarna_address_as_xt = array();
        foreach(xt_klarna_kp::address_key_mappings as $k_xt => $k_k)
        {
            $klarna_address_as_xt[$k_xt] = $klarna_address_data[$k_k];
            if($k_xt == 'customers_gender')
            {
                foreach (self::gender_salutation_mappings as $k => $val_arr)
                {
                    if (in_array($klarna_address_as_xt[$k_xt], $val_arr))
                    {
                        $klarna_address_as_xt[$k_xt] = $k;
                        break;
                    }
                }
            }
            if($k_xt == 'customers_country_code')
            {
                $klarna_address_as_xt[$k_xt] = strtoupper($klarna_address_as_xt[$k_xt]);
            }
        }

        /** check address exists in xt or not, create new if necessary */
        $klarna_address_as_xt = $this->checkAddress($klarna_address_as_xt, $customer);
        if(empty($klarna_address_as_xt['address_book_id']))
        {
            $klarna_address_as_xt['customers_dob'] = $dob;
            $klarna_address_as_xt['address_class'] = $addType;
            $klarna_address_as_xt['customers_id'] = $customer->customers_id;
            $klarna_address_as_xt['address_book_id'] = $customer->_writeAddressData($klarna_address_as_xt);
        }
        $klarna_address_as_xt = $customer->_buildAddressData($customer->customers_id, false, $klarna_address_as_xt['address_book_id']);
        return $klarna_address_as_xt;
    }

    public static function compareAddresses(array $a1, array $a2, $ignore = array())
    {

        $ignore = array_merge( $ignore,
            array(
                'address_book_id',
                'customers_id',
                'email',
                'customers_gender',
                'customers_suburb',
                //'customers_mobile_phone',
                //'customers_phone'
        ));

        $same = true;
        foreach(array_keys($a2) as $k)
        {
            if (in_array($k, $ignore)) continue;
            $val_k  = strtolower(trim($a2[$k]));
            $val_xt = strtolower(trim($a1[$k]));

            if ($val_xt != $val_k) {
                $same = false;
            }
        }

        return $same ? true : false; // evtl unterschiede zurückgeben
    }


    function createAccount($kp_order)
    {
        $c_data['customers_email_address']	= $kp_order['billing_address']['email'];

        $customer = sessionCustomer();

        if($kp_order['merchant_requested']['additional_checkbox']!=true)
        {
            $c_data['guest'] = 1;
        }
        $customer->_buildCustomerData($c_data, 'insert', false);

        return $customer->data_customer_id;
    }

    protected function loginCustomer($cID){
        global $xtPlugin;

        //throw new Exception('auto loginCustomer not allowed');

        $_SESSION['registered_customer'] = $cID;
        $_SESSION['customer']->_customer($cID);

        ($plugin_code = $xtPlugin->PluginCode('klarna_kp:login_customer')) ? eval($plugin_code) : false;
    }


    /**************************************************************************
     *
     *  Backend UI stuff
     *
     */
    public static function orderFormGrid_userButtonsWindows()
    {
        global $db;

        /**
         * button panel
         */

        $pnl = new PhpExt_Panel();

        $pnl->getTopToolbar()->addTextItem('Klarna', '<span style="font-weight: bold">Klarna</span>');

        $pnl->getTopToolbar()->addSeparator('|');
        $pnl->getTopToolbar()->addButton(1, TEXT_KLARNA_KP_DO_CAPTURE, null, new PhpExt_Handler(PhpExt_Javascript::stm('klarnaDoCaptureMultiple(orderds);')));

        $weeks = 5;
        $sql = "SELECT o.orders_id, o.kp_order_id, o.kp_order_ref, o.kp_order_status, o.kp_order_fraud_status, o.kp_order_data  
            FROM ".TABLE_ORDERS." o
            WHERE o.payment_code = 'xt_klarna_kp' 
            AND (o.kp_order_test_mode IS NULL OR o.kp_order_test_mode = 0)
            AND o.date_purchased > DATE_SUB(CURDATE(), INTERVAL ".$weeks." WEEK)
            AND o.kp_order_fraud_status = 'ACCEPTED'
            AND o.kp_order_status IN ('AUTHORIZED', 'PART_CAPTURED');";

        $rows = $db->GetArray($sql);
        if($rows && count($rows))
        {
            $amounts = [];
            foreach ($rows as $row)
            {
                $kp_order = json_decode($row['kp_order_data']);
                $amounts[$kp_order->purchase_currency] += $kp_order->remaining_authorized_amount;
            }
            $amounts_s = [];
            foreach($amounts as $k => $v)
            {
                $amounts_s[] = $k.' '.number_format($v/100,2,',', '.');
            }
            $amounts_s = implode(' / ', $amounts_s);
            $amounts_s = sprintf(constant('TEXT_KLARNA_HINT_OPEN_AMOUNTS'), $weeks, $amounts_s);
            $pnl->getTopToolbar()->addSeparator('||');
            $pnl->getTopToolbar()->addTextItem('hint_open_amounts', '&nbsp;&nbsp;<span class="blink" style="background-color: #ffa037; padding: 7px; border-radius: 7px">&nbsp;'.$amounts_s.'</span>&nbsp;&nbsp;');
        }

        $js_help_link = PhpExt_Javascript::stm("
                            window.open(\"https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/567181313/\", \"klarna-help-wnd\");
                        ");

        $pnl->getTopToolbar()->addSeparator('|||');
        $pnl->getTopToolbar()->addButton(3, '<i class="fa fa-question-circle" aria-hidden="true"></i>', null, new PhpExt_Handler($js_help_link));
/*
        $pnl->getTopToolbar()->addSeparator('||');
        $pnl->getTopToolbar()->addButton(2, TEXT_KLARNA_KP_DO_REFUND, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_do_refund)));

        $pnl->getTopToolbar()->addSeparator('|||');
        $pnl->getTopToolbar()->addButton(3, TEXT_KLARNA_KP_DO_RELEASE, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_do_release)));

        $pnl->getTopToolbar()->addSeparator('||||');
        $pnl->getTopToolbar()->addButton(4, TEXT_KLARNA_KP_DO_CANCEL, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_do_cancel)));
*/
        return $pnl;
    }

    public static function orderEdit_userButtonsWindows($orderId)
    {
        global $db;

        $js = '';

        $kp_order = klarna_kp::getKlarnaOrderFromXt($orderId);

        if(is_array($kp_order))
        {
            $refundableAmount = $kp_order['captured_amount'] - $kp_order['refunded_amount'];
            $remaining_authorized_amount = $kp_order['remaining_authorized_amount'];

            $infoText = ' (' . TEXT_ORDER . ' ' . $orderId . ')';
            $extF = new ExtFunctions();

            /**
             * capture window
             */
            $js_do_capture = '';
            if ($kp_order['remaining_authorized_amount'] > 0 && $kp_order['fraud_status'] == 'ACCEPTED' && $kp_order['status']!='CANCELLED')
            {
                $code = 'doCapture_kp';
                $panelData = self::getCaptureWindowPanel_kp($orderId, $kp_order['remaining_authorized_amount'] / 100, $kp_order);

                $remoteWindow = ExtAdminHandler::_RemoteModalWindow3(XT_KLARNA_KP . $infoText, $code, $panelData['panel'],
                    500, 225, 'window',
                    $panelData['buttons']);
                $remoteWindow->setId('doCapture_kp_wnd' . $orderId);

                $js_do_capture = $remoteWindow->getJavascript(false, "new_window") . ' new_window.show();';
            }

            /**
             * cancel window
             */
            $js_do_cancel = '';
            if ($kp_order['captured_amount'] == 0 && $kp_order['status']!='CANCELLED')
            {
                $code = 'doCancel_kp';
                //$extF->setCode($code);
                $panel = self::getCancelWindowPanel_kp($orderId);

                $cancelBtn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_CANCEL_LABEL"),
                    new PhpExt_Handler(PhpExt_Javascript::stm("
            klarnaCancel(" . $orderId . ");
            "))
                );
                $cancelBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT)
                    ->setId("TEXT_KLARNA_KP_CANCEL_LABEL" . $orderId)
                    ->setCssStyle('font-weight: bold')
                    ->setCssClass('bold')->attachListener('render',
                        new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                $('#'+this.id).find('button').addClass('bold');
            "))
                    );

                $remoteWindow = ExtAdminHandler::_RemoteWindow2(XT_KLARNA_KP . $infoText, $code, $panel, 400, 150, 'window');
                $remoteWindow->addButton($cancelBtn);
                $remoteWindow->setBodyStyle("")->setModal(true)
                    ->setId('doCancel_kp_wnd' . $orderId)
                    ->attachListener('render',
                        new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                this.setPosition(this.x, 20);
            "))
                    );
                $js_do_cancel = "var orders_id = " . $orderId . ";";
                $js_do_cancel .= $remoteWindow->getJavascript(false, "new_window") . ' new_window.show();';
            }

            /**
             * release window window
             */
            $js_do_release = '';
            if ($kp_order['captured_amount'] > 0 && $kp_order['remaining_authorized_amount'] > 0)
            {
                $code = 'doRelease_kp';
                $extF->setCode($code);
                $panel = self::getReleaseWindowPanel_kp($orderId, $kp_order['remaining_authorized_amount'] / 100);
                $remoteWindow = ExtAdminHandler::_RemoteWindow2(XT_KLARNA_KP . $infoText, $code, $panel, 400, 150, 'window');
                $remoteWindow->setBodyStyle("")->setModal(true)
                    ->setId('doRelease_kp_wnd' . $orderId)
                    ->attachListener('render',
                        new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                this.setPosition(this.x, 20);
            "))
                    );

                /**
                 *    Buttons
                 */
                $cancelBtn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_RELEASE_LABEL"),
                    new PhpExt_Handler(PhpExt_Javascript::stm("
                            klarnaRelease(".$orderId.");
                        "))
                    )
                    ->setId("TEXT_KLARNA_KP_CANCEL_LABEL" . $orderId)
                    ->setCssStyle('font-weight: bold')
                    ->setCssClass('bold')->attachListener('render',
                        new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                            $('#'+this.id).find('button').addClass('bold');
                        "))
                    );
                $cancelBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
                $cancelBtn->setId("TEXT_KLARNA_KP_RELEASE_LABEL".$orderId);
                $remoteWindow->addButton($cancelBtn);

                $js_do_release = "var orders_id = " . $orderId . ";";
                $js_do_release .= $remoteWindow->getJavascript(false, "new_window") . ' new_window.show();';
            }

            /**
             * refund window
             */
            $js_do_refund = '';
            if ($refundableAmount > 0)
            {
                $code = 'doRefund_kp';
                $extF->setCode($code);
                $panel = self::getRefundWindowPanel_kp($orderId, $refundableAmount / 100, $kp_order);
                $remoteWindow = ExtAdminHandler::_RemoteWindow2(XT_KLARNA_KP . $infoText, $code, $panel, 400, 325, 'window');
                $remoteWindow->setAutoHeight(true)->setBodyStyle("")->setModal(true)
                    ->setId('doRefund_kp_wnd' . $orderId)
                    ->attachListener('render',
                        new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                this.setPosition(this.x, 20);
            "))
                    );

                /**
                 *    Buttons
                 */
                $refundBtn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_REFUND_LABEL"),
                    new PhpExt_Handler(PhpExt_Javascript::stm("
                        klarnaRefund(".$orderId.", false);
                        "))
                    )
                    ->setId("TEXT_KLARNA_KP_CANCEL_LABEL" . $orderId)
                    ->setCssStyle('font-weight: bold')
                    ->setCssClass('bold')->attachListener('render',
                        new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                            $('#'+this.id).find('button').addClass('bold');
                        "))
                    );
                $refundBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
                $refundBtn->setId("TEXT_KLARNA_KP_REFUND_LABEL".$orderId);
                $remoteWindow->addButton($refundBtn);
                $js_do_refund = "var orders_id = " . $orderId . ";";
                $js_do_refund .= $remoteWindow->getJavascript(false, "new_window") . ' new_window.show();';
            }


            /**
             * details window
             */
            $code = 'details_kp';
            $extF->setCode($code);
            $panel = self::getDetailsWindowPanel_kp($kp_order, $orderId);
            $remoteWindow = ExtAdminHandler::_RemoteWindow2(XT_KLARNA_KP . $infoText, $code, $panel, 700, 700, 'window');
            $remoteWindow->setBodyStyle("")->setModal(true)
                ->setAutoHeight(false)->setHeight(700)
                ->setId('details_kp_wnd' . $orderId)
                ->attachListener('render',
                    new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                        this.setPosition(this.x, 20);
                    "))
                );

            $js_details = "var orders_id = " . $orderId . ";";
            $js_details .= $remoteWindow->getJavascript(false, "new_window") . ' new_window.show();';
            //$js_details = 'showCapturesForTracking(125, 10);';


            $js_update = PhpExt_Javascript::stm("
                            klarnaUpdateOrder(".$orderId.");
                        ");

            $js_help_link = PhpExt_Javascript::stm("
                            window.open(\"https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/567148547/\", \"klarna-help-wnd\");
                        ");

            /**
             * button panel
             */
            $pnl = new PhpExt_Panel();
            if ($remaining_authorized_amount > 0 ||
                $refundableAmount > 0 ||
                $kp_order['captured_amount'] == 0 || true
            )
            {
                $pnl = new PhpExt_Panel();

                $pnl->getTopToolbar()->setCssClass('toolbar-order');

                $pnl->getTopToolbar()->addTextItem('Klarna', '<span style="font-weight: bold">Klarna</span>');
                $pnl->getTopToolbar()->addSeparator('|');
                if ($remaining_authorized_amount > 0  && $kp_order['fraud_status'] == 'ACCEPTED' && $kp_order['status']!='CANCELLED')
                {
                    $pnl->getTopToolbar()->addButton(1, TEXT_KLARNA_KP_DO_CAPTURE, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_do_capture)));
                }
                else
                {
                    $btn = PhpExt_Toolbar_Button::createButton(TEXT_KLARNA_KP_DO_CAPTURE, null, null);
                    $btn->setDisabled(true);
                    $pnl->getTopToolbar()->addItem(1, $btn);
                }

                $pnl->getTopToolbar()->addSeparator('||');
                if ($refundableAmount > 0 && $kp_order['fraud_status'] == 'ACCEPTED')
                {
                    $pnl->getTopToolbar()->addButton(2, TEXT_KLARNA_KP_DO_REFUND, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_do_refund)));
                }
                else
                {
                    $btn = PhpExt_Toolbar_Button::createButton(TEXT_KLARNA_KP_DO_REFUND, null, null);
                    $btn->setDisabled(true);
                    $pnl->getTopToolbar()->addItem(2, $btn);
                }

                $pnl->getTopToolbar()->addSeparator('|||');
                if ($kp_order['captured_amount'] > 0 && $kp_order['remaining_authorized_amount'] > 0 && $kp_order['fraud_status'] == 'ACCEPTED')
                {
                    $pnl->getTopToolbar()->addButton(3, TEXT_KLARNA_KP_DO_RELEASE, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_do_release)));
                }
                else
                {
                    $btn = PhpExt_Toolbar_Button::createButton(TEXT_KLARNA_KP_DO_RELEASE, null, null);
                    $btn->setDisabled(true);
                    $pnl->getTopToolbar()->addItem(3, $btn);
                }

                $pnl->getTopToolbar()->addSeparator('||||');
                if ($kp_order['captured_amount'] == 0 && $kp_order['status']!='CANCELLED')
                {
                    $pnl->getTopToolbar()->addButton(4, TEXT_KLARNA_KP_DO_CANCEL, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_do_cancel)));
                }
                else
                {
                    $btn = PhpExt_Toolbar_Button::createButton(TEXT_KLARNA_KP_DO_CANCEL, null, null);
                    $btn->setDisabled(true);
                    $pnl->getTopToolbar()->addItem(4, $btn);
                }

                $pnl->getTopToolbar()->addSeparator('|||||');
                $pnl->getTopToolbar()->addButton(5, TEXT_KLARNA_KP_DETAILS, null, new PhpExt_Handler(PhpExt_Javascript::stm($js_details)));

                $pnl->getTopToolbar()->addSeparator('||||||');
                $pnl->getTopToolbar()->addButton(6, TEXT_KLARNA_KP_UPDATE_ORDER, null, new PhpExt_Handler($js_update));

                $pnl->getTopToolbar()->addSeparator('|||||||');
                $pnl->getTopToolbar()->addButton(7, '<i class="fa fa-question-circle" aria-hidden="true"></i>', null, new PhpExt_Handler($js_help_link));


                $pnl->setRenderTo(PhpExt_Javascript::variable("Ext.get('klarna_menu_bar_'+" . $orderId . ")"));
/*
                $js = PhpExt_Ext::onReady(
                    '$($("#memoContainer"+' . $orderId . ').parent().find(\'div.text\')[0]).css(\'margin-top\', \'30px\');',
                    '$("#memoContainer"+' . $orderId . ').parent().find(\'#order-menubar' . $orderId . '\').append("<div id=\'klarna_menu_bar_' . $orderId . '\'></div>");',
                    $pnl->getJavascript(false, "klarna")
                );
*/
            }

        }
        return $pnl;
    }

    static function getDetailsWindowPanel_kp($kp_order, $xt_order_id)
    {
        $kp_order_view = $kp_order;

        $tabPanel = new PhpExt_TabPanel();

        /**
         * overview
         */
        $infoPanel = self::getDetailsPanel(TEXT_OVERVIEW, 'details-overview.tpl.html', $kp_order_view, $xt_order_id);
        $tabPanel->addItem($infoPanel);

        /**
         * captures
         */
        if(is_array($kp_order_view['captures']) && count($kp_order_view['captures']))
        {
            $infoPanel = self::getDetailsCapturesPanel(TEXT_KP_CAPTURES, 'details-captures.tpl.html', $kp_order_view, $xt_order_id);
            $tabPanel->addItem($infoPanel);
        }

        /**
         * refunds
         */
        if(is_array($kp_order_view['refunds']) && count($kp_order_view['refunds']))
        {
            $infoPanel = self::getDetailsRefundsPanel(TEXT_KP_REFUNDS, 'details-refunds.tpl.html', $kp_order_view, $xt_order_id);
            $tabPanel->addItem($infoPanel);
        }

        /**
         * history
         */
        //$infoPanel = self::getDetailsPanel(TEXT_KP_HISTORY, 'details-history.tpl.html', $kp_order_view, $xt_order_id);
        //$tabPanel->addItem($infoPanel);

        $tabPanel->setActiveItem(0);

        return $tabPanel;

    }

    private static function getDefaultDetailsPanel($xt_order_id)
    {
        static $counter = 0;
        $panel = new PhpExt_Panel();
        $panel
            ->setId("kpDetailsPanel_{$xt_order_id}_{$counter}")
            ->setBorder(false)
            ->setAutoHeight(false)->setHeight(400)->setAutoScroll(true)
            ->setBodyStyle('padding: 10px;')
            ->attachListener('activate',
                new PhpExt_Listener(PhpExt_Javascript::functionDef('initAccordion_kpDetailsPanel_', "
                    $( function() { 
                        $( '#kpDetailsPanel_{$xt_order_id}_{$counter} .accordion' ).accordion({
                            collapsible: true,
                            active: false,
                            autoHeight:false
                        });
                        if ($( '#kpDetailsPanel_{$xt_order_id}_{$counter} .accordion' ).length > 0) Ext.getCmp('kpDetailsPanel_{$xt_order_id}_{$counter}').removeListener('activate', initAccordion_kpDetailsPanel_);
                    } );
                ")));
        $counter++;
        return $panel;
    }

    static private function getDetailsPanel($title, $tplFile, $view_data, $xt_order_id)
    {
        $template = new Template();
        $template->getTemplatePath($tplFile, 'xt_klarna_kp', 'admin', 'plugin');

        $view_items = array(
            'order_lines' => $view_data['order_lines']
        );
        $unset_keys = ['order_lines','captures','refunds'];
        foreach($unset_keys as $k)
        {
            unset($view_data[$k]);
        }

        $tpl_data = array('order_id' => $view_data['order_id']);
        $extend_auth_time_html = $template->getTemplate('', 'auth-time-extend.tpl.html', $tpl_data);
        $view_data['expires_at'] .= '&nbsp;'.$extend_auth_time_html;

        $view_items['data'] = $view_data;


        $tpl_data = array(
            'view_items' => $view_items,
        );
        $html = $template->getTemplate('', $tplFile, $tpl_data);
        $html = preg_replace( "/\r|\n/", "", $html);

        $panel = self::getDefaultDetailsPanel($xt_order_id);
        $panel->setHtml($html)
            ->setTitle($title);

        return $panel;
    }

    static private function getDetailsCapturesPanel($title, $tplFile, $view_data, $xt_order_id)
    {
        $template = new Template();
        $template->getTemplatePath($tplFile, 'xt_klarna_kp', 'admin', 'plugin');

        $tpl_data['order_id'] = $view_data['order_id'];

        foreach($view_data['captures'] as &$capture)
        {
            $tpl_data['capture_id'] =  $capture['capture_id'];
            $capture['html_name'] = $template->getTemplate('', 'capture-name.tpl.html', $tpl_data);
        }

        $tpl_data = array(
            'view_items' => $view_data
        );
        $html = $template->getTemplate('', $tplFile, $tpl_data);
        $html = preg_replace( "/\r|\n/", "", $html);

        $panel = self::getDefaultDetailsPanel($xt_order_id);
        $panel->setHtml($html)
            ->setTitle($title);

        return $panel;
    }

    static private function getDetailsRefundsPanel($title, $tplFile, $view_data, $xt_order_id)
    {
        $template = new Template();
        $template->getTemplatePath($tplFile, 'xt_klarna_kp', 'admin', 'plugin');


        $tpl_data = array(
            'view_items' => $view_data,
        );
        $html = $template->getTemplate('', $tplFile, $tpl_data);
        $html = preg_replace( "/\r|\n/", "", $html);

        $panel = self::getDefaultDetailsPanel($xt_order_id);
        $panel->setHtml($html)
            ->setTitle($title);

        return $panel;
    }


    static function getCaptureWindowPanel_kp($xt_order_id, $remainingCaptureAmount, $kp_order = null)
    {
        global $admin_order_edit, $price, $currency, $xtPlugin;

        static $panelData = null;

        if(empty($panelData))
        {
            $Panel = new PhpExt_Form_FormPanel('doCapture_kp');
            $Panel->setId('doCapture_kp' . $xt_order_id)
                ->setTitle(__define('TEXT_KLARNA_KP_DO_CAPTURE'))
                ->setAutoWidth(true)
                ->setAutoHeight(true)
                ->setBodyStyle('padding: 10px;')
                ->setUrl("adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=capture&orders_id=" . $xt_order_id . '&sec=' . $_SESSION['admin_user']['admin_key']);


            $eF = new ExtFunctions();
            $eF->setSetting('gridType', 'EditGrid');

            //$remainingCaptureAmount = '62.31';
            $maxCaptureAmountFormated = $price->_StyleFormat($remainingCaptureAmount);
            $maxCaptureAmount = number_Format($remainingCaptureAmount, 2, $currency->dec_point, '');
            $infoPanel = new PhpExt_Panel();
            $infoPanel->setHtml(TEXT_KLARNA_KP_MAX_AVAIL_CAPTURE_AMOUNT . ': <a href="javascript:void(0)" onclick="klarnaApplyCaptureAmount(\'' . $maxCaptureAmount . '\',' . $xt_order_id . ')">' . $maxCaptureAmountFormated . '</a><br />&nbsp;<br />')->setBorder(false);
            $Panel->addItem($infoPanel);

            if($kp_order)
            {
                $orderLinePanel = new PhpExt_Panel();
                $orderLinePanel->setBorder(false)
                    ->setCssStyle('padding-bottom:15px; font-size: 1.1em');

                $skip_array = ['discount'];
                foreach($kp_order['order_lines'] as $k => $ol)
                {
                    if($ol["reference"] == 'freeshipping' && $ol["type"] == 'discount')
                    {
                        $skip_array[] = 'shipping_fee';
                        break;
                    }
                }
                foreach($kp_order['order_lines'] as $k => $ol)
                {
                    if(in_array($ol['type'], $skip_array)) continue;

                    $cb = PhpExt_Form_Checkbox::createCheckbox('order_line['.$k.']');

                    $prefix = '';
                    if($ol["type"] == 'shipping_fee') $prefix = __define('TEXT_SHIPPING').' ';

                    $cb->setLabelSeparator('')
                        ->setBoxLabel($prefix . $ol['name'].' ('.$ol['reference'].')')
                        ->setInputValue($ol["type"].'#'.$ol['reference']);
                    $orderLinePanel->addItem($cb);
                }
                $Panel->addItem($orderLinePanel);
            }

            $tf = PhpExt_Form_TextField::createTextField('capture_amount', __define('TEXT_KLARNA_KP_CAPTURE_AMOUNT'));
            $tf->setValidator(new PhpExt_Handler(PhpExt_Javascript::stm('return klarnaCheckCaptureAmount(' . $xt_order_id . ');')));
            $tf->setAllowBlank(false)->setValue("");
            $Panel->addItem($tf);

            $tf = PhpExt_Form_TextArea::createTextArea('capture_description', __define('TEXT_COMMENTS'). '(max 250)');
            $tf->setValidator(new PhpExt_Handler(PhpExt_Javascript::stm('return klarnaCheckCaptureDescription('.$xt_order_id.');')));
            $tf->setValue("")->setHeight(50)->setWidth(240);
            $Panel->addItem($tf);

            $resize_listener = new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                 syncRemoteWindowSizeKlarna('".'doCapture_kp_wnd' . $xt_order_id."')
            "));

            if (isset($xtPlugin->active_modules['xt_ship_and_track']))
            {
                require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_tracking.php';
                $trackings = xt_tracking::getTrackingForOrder($xt_order_id);
                if(count($trackings))
                {
                    $fs = new PhpExt_Form_FieldSet();
                    $fs->attachListener('collapse', $resize_listener)->attachListener('collapse', $resize_listener);
                    $fs->setBorder(true)
                        ->setId('capture_trackings' . $xt_order_id)
                        ->setBodyBorder(true)
                        ->setCheckboxToggle(true)
                        ->setCheckboxName('add_service_dhl')
                        ->setTitle(TEXT_SC_TRACKING_URL)
                        ->setAutoHeight(true)
                        ->setDefaults(new PhpExt_Config_ConfigObject(array()))
                        ->setCollapsed(false);
                    foreach($trackings as $t)
                    {
                        $cb = PhpExt_Form_Checkbox::createCheckbox('capture_tracking['.$t['id'].']');
                        $cb->setLabelSeparator('')
                            ->setBoxLabel($t['shipper_name'].' '.$t['tracking_code'])
                        ->setValue($t['id']);
                        $fs->addItem($cb);
                    }
                    $Panel->addItem($fs);
                }

            }

            /**
             *    hidden fields
             */
            $Panel->addItem(PhpExt_Form_Hidden::createHidden('max_capture_amount', $remainingCaptureAmount));
            $Panel->addItem(PhpExt_Form_Hidden::createHidden('release', 0));
            if ($xt_order_id)
            {
                $hidden = PhpExt_Form_Hidden::createHidden('order_total_price', number_Format($admin_order_edit->order_data['order_total']['total']['plain'], 2, ',', ''));
                $hidden->setId('order_total_price' . $xt_order_id);
                $Panel->addItem($hidden);
            }
            else
            {
                $Panel->addItem(PhpExt_Form_Hidden::createHidden('order_ids', 0));
            }

            /**
             *    Buttons
             */
            $captureReleaseBtn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_CAPTURE_RELEASE_LABEL"),
                new PhpExt_Handler(PhpExt_Javascript::stm("
                    klarnaCapture(" . $xt_order_id . ", true);
                    "))
            );
            $captureReleaseBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
            $captureReleaseBtn->setId("TEXT_KLARNA_KP_CAPTURE_RELEASE_LABEL" . $xt_order_id);

            $captureBtn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_CAPTURE_LABEL"),
                new PhpExt_Handler(PhpExt_Javascript::stm("
                    klarnaCapture(" . $xt_order_id . ", false);
                    "))
            );
            $captureBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT)
                ->setId("TEXT_KLARNA_KP_CANCEL_LABEL" . $xt_order_id)
                ->setCssStyle('font-weight: bold')
                ->setCssClass('bold')->attachListener('render',
                    new PhpExt_Listener(PhpExt_Javascript::functionDef(null, "
                        $('#'+this.id).find('button').addClass('bold');
                    "))
                );
            $captureBtn->setId("TEXT_KLARNA_KP_CAPTURE_LABEL" . $xt_order_id);

            $panelData['panel'] = $Panel;
            $panelData['buttons'] = array($captureReleaseBtn, $captureBtn);
        }

        return $panelData;
    }

    static function getRefundWindowPanel_kp($orders_id, $remainingRefundAmount, $kp_order = null)
    {
        global $price, $currency;

        $Panel = new PhpExt_Form_FormPanel('doRefund_kp');
        $Panel->setId('doRefund_kp'.$orders_id)
            ->setTitle(__define('TEXT_KLARNA_KP_DO_REFUND'))
            ->setAutoWidth(true)
            ->setAutoHeight(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=refund&orders_id=".$orders_id.'&sec='.$_SESSION['admin_user']['admin_key']);


        $eF = new ExtFunctions();
        $eF->setSetting('gridType', 'EditGrid');

        $maxRefundAmountFormated = $price->_StyleFormat($remainingRefundAmount);
        $maxRefundAmount = number_Format($remainingRefundAmount,2, $currency->dec_point, '');
        $infoPanel = new PhpExt_Panel();
        $infoPanel->setHtml(TEXT_KLARNA_KP_MAX_AVAIL_REFUND_AMOUNT.': <a href="javascript:void(0)" onclick="klarnaApplyRefundAmount(\''.$maxRefundAmount.'\','.$orders_id.')">'.$maxRefundAmountFormated.'</a><br />&nbsp;<br />')->setBorder(false);
        $Panel->addItem($infoPanel);

        $tf = PhpExt_Form_TextField::createTextField('refund_amount', __define('TEXT_KLARNA_KP_REFUND_AMOUNT'));
        $tf->setValidator(new PhpExt_Handler(PhpExt_Javascript::stm('return klarnaCheckRefundAmount('.$orders_id.');')));
        $tf->setValue("");
        $Panel->addItem($tf);

        if($kp_order)
        {
            $orderLinePanel = new PhpExt_Panel();
            $orderLinePanel->setBorder(false)
                ->setCssStyle('padding-bottom:15px; font-size: 1.1em');
            foreach($kp_order['order_lines'] as $k => $ol)
            {
                $cb = PhpExt_Form_Checkbox::createCheckbox('order_line['.$k.']');
                $cb->setLabelSeparator('')
                    ->setBoxLabel($ol['name'].' ('.$ol['reference'].')')
                    ->setInputValue($ol["type"].'#'.$ol['reference']);
                $orderLinePanel->addItem($cb);
            }
            $Panel->addItem($orderLinePanel);
        }

        $tf = PhpExt_Form_TextArea::createTextArea('refund_description', __define('TEXT_COMMENTS'). '(max 250)');
        $tf->setValidator(new PhpExt_Handler(PhpExt_Javascript::stm('return klarnaCheckRefundDescription('.$orders_id.');')));
        $tf->setValue("")->setHeight(50)->setWidth(240);
        $Panel->addItem($tf);

        /**
         *    hidden fields
         */
        $Panel->addItem(PhpExt_Form_Hidden::createHidden('max_refund_amount', $remainingRefundAmount));

        return $Panel;
    }

    static function getCancelWindowPanel_kp($orders_id)
    {
        $Panel = new PhpExt_Form_FormPanel('doCancel_kp');
        $Panel->setId('doCancel_kp'.$orders_id)
            //->setTitle(__define('TEXT_KLARNA_KP_DO_CANCEL'))
            ->setAutoWidth(true)
            ->setAutoHeight(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=cancel&orders_id=".$orders_id.'&sec='.$_SESSION['admin_user']['admin_key']);

        $eF = new ExtFunctions();
        $eF->setSetting('gridType', 'EditGrid');

        $infoPanel = new PhpExt_Panel();
        $infoPanel->setHtml(TEXT_KLARNA_KP_ASK_CANCEL.'<br />&nbsp;<br />')->setBorder(false);
        $Panel->addItem($infoPanel);

        /**
         *    hidden fields
         */
        if($orders_id)
        {
        }
        else {
            $Panel->addItem(PhpExt_Form_Hidden::createHidden('order_ids', 0));
        }

        /**
         *    Buttons
         */
        /*
        $cancelBtn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_CANCEL_LABEL"),
            new PhpExt_Handler(PhpExt_Javascript::stm("
            klarnaCancel(".$orders_id.");
            "))
        );
        $cancelBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
        $cancelBtn->setId("TEXT_KLARNA_KP_CANCEL_LABEL".$orders_id);
        $Panel->addButton($cancelBtn);
*/
        return $Panel;
    }

    function linkTrackingToCapture($data)
    {
        $result = new stdClass();
        $result->success = true;
        try
        {
            $capture_ids = array_keys($data['link_capture_to_tracking']);
            if (count($capture_ids))
            {
                $kp_order = klarna_kp::getKlarnaOrderFromXt($data['xt_orders_id']);

                foreach ($kp_order['captures'] as $c)
                {
                    if (in_array($c['capture_id'], $capture_ids))
                    {
                        $shipping_info = $this->buildShippingInfo(array($data['tracking_id']));
                        klarna_kp::addShippingInfo($kp_order['order_id'], $c['capture_id'], $shipping_info);
                    }
                }
            }
        }catch (Exception $e)
        {
            // todo in frontend anzeige einbinden  > buildMsgFromException
            $result->success = false;
            $result->msg = $e->getMessage();
        }
        return $result;
    }

    function getCapturesPanel($data)
    {
        global $price;

        $kp_order = klarna_kp::getKlarnaOrderFromXt($data['xt_orders_id']);

        if(is_array($kp_order) && is_array($kp_order['captures']) && count($kp_order['captures']))
        {
            $Panel = new PhpExt_Form_FormPanel('doLinkCapture_kp');
            $Panel->setId('doLinkCapture_kp' . $data['xt_orders_id'])
                ->setTitle(__define('TEXT_KLARNA_KP_POSSIBLE_CAPTURES'))
                ->setAutoWidth(true)
                ->setAutoHeight(true)
                ->setBodyStyle('padding: 10px 0;')
                ->setUrl("adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=linkTrackingToCapture&xt_orders_id=" . $data['xt_orders_id'] . '&sec=' . $_SESSION['admin_user']['admin_key']);

            $eF = new ExtFunctions();
            $eF->setSetting('gridType', 'EditGrid');

            foreach ($kp_order['captures'] as $c)
            {
                $price_formated = $price->_StyleFormat($c['captured_amount'] / 100);
                $cb = PhpExt_Form_Checkbox::createCheckbox('link_capture_to_tracking[' . $c['capture_id'] . ']');
                $cb->setLabelSeparator('')
                    ->setBoxLabel(date_format(new DateTime($c['captured_at']), "Y-m-d H:m:i") . ' - ' . $price_formated)
                    ->setValue($c['capture_id']);
                $Panel->addItem($cb);
            }

            $Panel->addItem(PhpExt_Form_Hidden::createHidden('xt_order_ids', $data['xt_orders_id']));
            $Panel->addItem(PhpExt_Form_Hidden::createHidden('tracking_id', $data['tracking_id']));

            /*
            $btn = PhpExt_Button::createTextButton(__define("TEXT_KLARNA_KP_DO_LINK"),
                new PhpExt_Handler(PhpExt_Javascript::stm("
                            klarnaLinkCapture('" . $data['xt_orders_id'] . "');
                        "))
            )
                //->setId("TEXT_KLARNA_KP_CANCEL_LABEL" . $data['xt_orders_id'])
                ->setCssStyle('font-weight: bold')
                ->setCssClass('bold')
                ->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
            $Panel->addButton($btn);
             */

            $Panel->setRenderTo(PhpExt_Javascript::variable("Ext.get('" . $Panel->getId() . "_div')"));

            $js = PhpExt_Ext::OnReady(
                PhpExt_Javascript::stm(PhpExt_QuickTips::init()),
                $Panel->getJavascript(false, "croot")
            );

            return '<div id="' . $Panel->getId() . '_div"></div><script type="text/javascript">' . $js . '</script>';
        }
        $js = "var wnd = Ext.getCmp('doLinkCapture_kp_wndRemoteWindow');
        if(typeof wnd != 'undefined') wnd.close();
        ";
        return '<script type="text/javascript">' . $js . '</script>';
    }

    static function getReleaseWindowPanel_kp($orders_id, $amount)
    {
        global $price;

        $Panel = new PhpExt_Form_FormPanel('doRelease_kp');
        $Panel->setId('doRelease_kp'.$orders_id)
            ->setTitle(__define('TEXT_KLARNA_KP_DO_RELEASE'))
            ->setAutoWidth(true)
            ->setAutoHeight(true)
            ->setBodyStyle('padding: 10px;')
            ->setUrl("adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp&pg=release&orders_id=".$orders_id.'&sec='.$_SESSION['admin_user']['admin_key']);

        $eF = new ExtFunctions();
        $eF->setSetting('gridType', 'EditGrid');

        $amountFormated = $price->_StyleFormat($amount);

        $infoPanel = new PhpExt_Panel();
        $infoPanel->setHtml(TEXT_KLARNA_KP_ASK_RELEASE." $amountFormated<br />&nbsp;<br />")->setBorder(false);
        $Panel->addItem($infoPanel);

        /**
         *    hidden fields
         */
        if($orders_id)
        {
        }
        else {
            $Panel->addItem(PhpExt_Form_Hidden::createHidden('order_ids', 0));
        }

        return $Panel;
    }

    private function buildShippingInfo(array $tracking_ids)
    {
        global $xtPlugin;

        $shipping_info = array();
        if (isset($xtPlugin->active_modules['xt_ship_and_track']))
        {
            require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_ship_and_track/classes/class.xt_tracking.php';

            foreach ($tracking_ids as $id)
            {
                $tracking = tracking::getTracking($id);

                if(is_array($tracking[0]))
                {
                    $shipping_info[] = array(
                        'shipping_company' => $tracking[0]['shipper_name'],
                        //'shipping_method' => 'Own', //$tracking[0][''],
                        'tracking_number' => $tracking[0]['tracking_code'],
                        'tracking_uri' => str_replace('[TRACKING_CODE]', $tracking[0]['tracking_code'], $tracking[0]['shipper_tracking_url']),
                        'return_shipping_company' => null, //$tracking[0][''],
                        'return_tracking_number' => null, //$tracking[0][''],
                        'return_tracking_uri' => null, //$tracking[0][''],
                    );
                }
            }
        }
        return $shipping_info;
    }

    private static function buildMsgFromException(Exception $ex, stdClass &$msg, $doLog = true)
    {

    }


    public function registerPage($data)
    {
        echo '
        <div style="padding: 50px">
    <h2>registration reminder</h2>
    <p>prefilled form</p>
</div>
        ';
    }


    public static function getMerchantIdForOrder($xt_order_id)
    {
        global $db;
        $mid = false;

        $key = '_KLARNA_CONFIG_KP_MID';
        if(constant('_KLARNA_CONFIG_KP_TESTMODE' == 1))
        {
            $key = '_KLARNA_CONFIG_KP_MID_TEST';
        }
        $store_id = $db->GetOne("SELECT o.shop_id FROM ".TABLE_ORDERS." o LEFT JOIN ".TABLE_MANDANT_CONFIG." s ON s.shop_id = o.shop_id WHERE o.orders_id=?", array($xt_order_id));
        if($store_id)
        {
            $mid = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store_id. " WHERE config_key = '_KLARNA_CONFIG_KP_MID' ");
        }
        else {
            //throw new Exception('No store found for xt-order ['.$xt_order_id.']');
        }
        return $mid;
    }

    public function openKlarnaBackend($data)
    {
        echo '
        <iframe src="https://merchants.klarna.com" style="height: 100%; width:100%"/>
        ';
    }
}