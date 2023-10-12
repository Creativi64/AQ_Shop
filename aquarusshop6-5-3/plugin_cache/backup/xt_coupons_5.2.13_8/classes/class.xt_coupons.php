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

require_once _SRV_WEBROOT . 'plugins/xt_coupons/classes/constants.php';

class xt_coupons
{
    protected $_table = TABLE_COUPONS;
    protected $_table_lang = TABLE_COUPONS_DESCRIPTION;
    protected $_table_seo = null;
    protected $_master_key = 'coupon_id';
    public $error_info = '';

    public $couponCode;
    public $data = array();
    public $description = array();

    public function __construct ($id = 0)
    {
        if (USER_POSITION != 'admin') {
            if ($id != 0) {
                global $db, $language;
                $record = $db->Execute("SELECT * FROM " . TABLE_COUPONS . " where coupon_id=? limit 1",array((int)$id));
                if ($record->RecordCount() == 1) {
                    if ($record->fields['coupon_status']) {
                        $this->couponCode = $record->fields['coupon_code'];
                        $this->data = current($record->getArray());
                        $rs = $db->Execute("SELECT * FROM " . TABLE_COUPONS_DESCRIPTION . " 
                                where coupon_id=? and language_code = ? limit 1",array((int)$id,$language->code));
                        if ($rs->RecordCount() == 1) {
                            $this->description = current($rs->getArray());
                        }
                    } else {
                        return false;
                    }
                    $record->Close();
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            $this->getPermission();
            return false;
        }
    }
    
    function getPermission(){
        global $store_handler, $customers_status, $xtPlugin;
    
            $this->perm_array = array(
                    'shop_perm' => array(
                            'type'=>'shop',
                            'table'=>TABLE_COUPONS_PERMISSION,
                            'key'=>$this->_master_key,
                            'simple_permissions' => 'true',
                            'simple_permissions_key' => 'permission_id',
                            'pref'=>'c'
                    )
            );

            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getPermission')) ? eval($plugin_code) : false;

            $this->permission = new item_permission($this->perm_array);

            return $this->perm_array;
    }

    function setPosition ($position)
    {
        $this->position = $position;
    }

    function _getParams ()
    {
        global $language, $xtPlugin;

        $params = array();

        $header['coupon_id'] = array('type' => 'hidden');
        $header['permission_id'] = array('type' => 'hidden');
        $header['coupon_amount'] = array('type' => 'hidden');
        $header['coupon_percent'] = array('type' => 'hidden');
        $header['coupon_free_shipping'] = array('type' => 'hidden');
        $header['coupon_order_ordered'] = array('readonly' => 1);

        $header['compaign_id'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?systemstatus=campaign');

        $header['coupon_type'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=coupon_type&plugin_code=xt_coupons');

        //$header['customers_status'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=customers_status');

        $header['customers_status'] = array('type' => 'itemselect', 'readonly'=>false, 'required' => true,
            'url' => 'DropdownData.php?get=coupon_customers_status',
            'valueUrl' => 'adminHandler.php?plugin=xt_coupons&load_section=xt_coupons&pg=coupons_get_saved_customers_status&id=' . $this->url_data['edit_id'].'&sec='.$_SESSION['admin_user']['admin_key']);

        $header['coupon_tax_class'] = array('type' => 'dropdown', 'url' => 'DropdownData.php?get=tax_classes');
		$header['coupon_can_decrease_shipping'] = array('type' => 'status');
        if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
            foreach (xt_promo_configuration::$_social_networks as $social_network) {
                $header['xt_social_' . $social_network] = array('type' => 'status');
            }
        }

        ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_getParams_header')) ? eval($plugin_code) : false;

        $params['header'] = $header;
        $params['display_searchPanel'] = true;
        $params['master_key'] = $this->_master_key;

        $rowActions = array();
        $extF = new ExtFunctions();

        // Row_Action Categories
        if($this->url_data["new"] != 'true')
        {
            $rowActions[] = array('iconCls' => 'coupon_categories', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_COUPON_CATEGORIES);
            $js = '';
            if ($this->url_data['coupon_id'])
            {
                $js .= "var coupon_id = " . $this->url_data['coupon_id'] . ";";
            }
            else
            {
                if ($this->url_data['edit_id'])
                {
                    $js .= "var coupon_id = " . $this->url_data['edit_id'] . ";";
                }
                else
                {
                    $js = "var coupon_id = record.id;";
                }
            }

            $js .= $extF->_RemoteWindow("TEXT_COUPON_CATEGORIES", "TEXT_COUPONS", "adminHandler.php?plugin=xt_coupons&load_section=xt_coupons_categories&pg=getTreePanel&coupon_id='+coupon_id+'", '', array(), 800, 600) . ' new_window.show();';
        }
        else {
            $js = $extF->MsgAlert("Bitte erst speichern / Save first", "!");
        }
        $rowActionsFunctions['coupon_categories'] = $js;

        // Row_Action Products
        if($this->url_data["new"] != 'true')
        {
            $rowActions[] = array('iconCls' => 'coupon_products', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_COUPON_PRODUCTS);

            $js = '';
            if ($this->url_data['coupon_id'])
            {
                $js .= "var coupon_id = " . $this->url_data['coupon_id'] . ";";
            }
            else
            {
                if ($this->url_data['edit_id'])
                {
                    $js .= "var coupon_id = " . $this->url_data['edit_id'] . ";";
                }
                else
                {
                    $js = "var coupon_id = record.id;";
                }
            }

            $js .= $extF->_RemoteWindow("TEXT_COUPON_PRODUCTS", "TEXT_COUPONS", "adminHandler.php?plugin=xt_coupons&load_section=xt_coupons_products&pg=overview&coupon_id='+coupon_id+'", '', array(), 800, 600) . ' new_window.show();';

            //$rowActions[] = array('iconCls' => 'coupon_products', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_COUPON_PRODUCTS);
        }
        else {
            $js = $extF->MsgAlert("Bitte erst speichern / Save first", "!");
        }
        $rowActionsFunctions['coupon_products'] = $js;


        $js = '';
        // Row_Action Customer
        $rowActions[] = array('iconCls' => 'coupon_customer', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_COUPON_CUSTOMER);
        if($this->url_data["new"] != 'true')
        {
            if ($this->url_data['coupon_id'])
            {
                $js .= "var coupon_id = " . $this->url_data['coupon_id'] . ";";
            }
            else
            {
                if ($this->url_data['edit_id'])
                {
                    $js .= "var coupon_id = " . $this->url_data['edit_id'] . ";";
                }
                else
                {
                    $js = "var coupon_id = record.id;";
                }
            }

            $js .= $extF->_RemoteWindow("TEXT_COUPON_CUSTOMER", "TEXT_COUPONS", "adminHandler.php?plugin=xt_coupons&load_section=xt_coupons_customers&pg=overview&coupon_id='+coupon_id+'", '', array(), 800, 600) . ' new_window.show();';
        }
        else {
            $js .= $extF->MsgAlert("Bitte erst speichern / Save first", "!");
        }
        $rowActionsFunctions['coupon_customer'] = $js;

        ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_getParams_row_actions')) ? eval($plugin_code) : false;

        $params['rowActions'] = $rowActions;
        $params['rowActionsFunctions'] = $rowActionsFunctions;

        if (!$this->url_data['edit_id'] && $this->url_data['new'] != true) {
            $params['include'] = array('coupon_id', 'coupon_name_' . $language->code, 'coupon_status', 'coupon_amount', 'coupon_percent', 'coupon_free_shipping', 'coupon_expire_date', 'codes_unasigned', 'codes_assigned', 'redeem_amount');
            if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                foreach (xt_promo_configuration::$_social_networks as $social_network) {
                    $params['include'][] = 'xt_social_' . $social_network;
                }
            }
        } else {
            $params['exclude'] = array('coupon_created_ip', 'coupon_last_modified_date');
            if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                $params['exclude'][] = 'coupon_internal_status';
            }
        }
        
        if(_SYSTEM_SIMPLE_GROUP_PERMISSIONS=='true' ){
            $set_perm = new item_permission($this->perm_array);
            $params['exclude'] = $set_perm->_excludeFields($params['exclude']);
        }

        ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_getParams_bottom')) ? eval($plugin_code) : false;

        return $params;
    }

    function _get ($ID = 0)
    {
        global $xtPlugin, $db, $language, $filter;

        $obj = new stdClass;

        if ($this->position != 'admin')
            return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }
        
      if(_SYSTEM_SIMPLE_GROUP_PERMISSIONS == 'false' ){
                $permissions = $this->perm_array;
            }else{
                $permissions = '';
            }
        if ($this->url_data['get_data']) {
            if ($this->url_data['query'] != '') {
                $query = $filter->_filter($this->url_data['query']);
                $sql_where = '';
                $data = array();
                $data_all = array();
                $sql = '';
                $sql .= "SELECT c.coupon_id FROM ".$this->_table . " as c
                            INNER JOIN " . $this->_table_lang . " as cd ON c.coupon_id = cd.coupon_id 
                         WHERE (c.coupon_code LIKE ? OR
                                cd.coupon_name LIKE ? OR
                                cd.coupon_description LIKE ?";
                $sec_key = array('%'.$query.'%','%'.$query.'%','%'.$query.'%');
                if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                    $sql .= " OR cd.social_network_message LIKE ?')";
                    $sql .= " AND c.coupon_internal_status != 1 ";
                    array_push($sec_key,'%'.$query.'%');
                } else {
                    $sql .= " ) ";
                }
                $record = $db->Execute($sql,$sec_key);
                if ($record->RecordCount() > 0) {
                    while (!$record->EOF) {
                        $data_all[] = $record->fields;
                        $data[] = $record->fields['coupon_id'];
                        $record->MoveNext();
                    }
                    $record->Close();

                    if (!isset($this->sql_limit)) {
                        $this->sql_limit = "0,25";
                    }
                    $sql_where = ' coupon_id in (' . implode(', ', $data) . ')';
                }
                
                
                $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, $this->sql_limit, $permissions);

                ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_get_data_table_data')) ? eval($plugin_code) : false;

                $data = $table_data->getData();

                foreach ($data as $key => $arr) {

                    $query = "SELECT count(*) as count FROM " . TABLE_COUPONS_TOKEN . " WHERE coupon_id=? and coupon_token_order_id='0'";
                    $rs = $db->Execute($query,array($arr['coupon_id']));
                    $records = array();
                    $records['codes_unasigned'] = $rs->fields['count'];

                    $query = "SELECT count(*) as count FROM " . TABLE_COUPONS_REDEEM . " WHERE coupon_id=?";
                    $rs = $db->Execute($query,array($arr['coupon_id']));
                    $records['codes_assigned'] = $rs->fields['count'];

                    $query = "SELECT sum(redeem_amount) as amount FROM " . TABLE_COUPONS_REDEEM . " WHERE coupon_id=? ";
                    $rs = $db->Execute($query,array($arr['coupon_id']));
                    $records['redeem_amount'] = round($rs->fields['amount'], 2);

                    if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                        foreach (xt_promo_configuration::$_social_networks as $social_network) {
                            $query = "SELECT coupon_id, social_network FROM " . TABLE_COUPONS_SOCIAL_NETWORKS . " 
                                    WHERE coupon_id=? AND social_network = ?";
                            $rs = $db->Execute($query,array($arr['coupon_id'],$social_network));
                            $records['xt_social_' . $social_network] = (($rs->RecordCount() > 0) ? 1 : 0);
                        }
                    }

                    ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_get_data_foreach')) ? eval($plugin_code) : false;

                    $data[$key] = array_merge($arr, $records);
                }

            } else {

                if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                    if ($sql_where !== '') {
                        $sql_where = ' coupon_internal_status != 1';
                    }
                }
                
                
                $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where,'',$permissions);

                ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:table_data')) ? eval($plugin_code) : false;

                $data = $table_data->getData();

                // query for ids
                if (count($data) > 0) {
                    foreach ($data as $key => $arr) {

                        $query = "SELECT count(*) as count FROM " . TABLE_COUPONS_TOKEN . " WHERE coupon_id=? and coupon_token_order_id='0'";
                        $rs = $db->Execute($query,array($arr['coupon_id']));
                        $records = array();
                        $records['codes_unasigned'] = $rs->fields['count'];

                        $query = "SELECT count(*) as count FROM " . TABLE_COUPONS_REDEEM . " WHERE coupon_id=?";
                        $rs = $db->Execute($query,array($arr['coupon_id']));
                        $records['codes_assigned'] = $rs->fields['count'];

                        $query = "SELECT sum(redeem_amount) as amount FROM " . TABLE_COUPONS_REDEEM . " WHERE coupon_id=?";
                        $rs = $db->Execute($query,array($arr['coupon_id']));
                        $records['redeem_amount'] = round($rs->fields['amount'], 2);

                        if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                            foreach (xt_promo_configuration::$_social_networks as $social_network) {
                                $query = "SELECT coupon_id, social_network FROM " . TABLE_COUPONS_SOCIAL_NETWORKS . " 
                                        WHERE coupon_id=? AND social_network = ?";
                                $rs = $db->Execute($query,array($arr['coupon_id'],$social_network));
                                $records['xt_social_' . $social_network] = (($rs->RecordCount() > 0) ? 1 : 0);
                            }
                        }

                        ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:table_data_foreach')) ? eval($plugin_code) : false;

                        $data[$key] = array_merge($arr, $records);
                    }
                }
            }

        } elseif ($ID) {
              
            $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'', '', $permissions);
            ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:id_table_data')) ? eval($plugin_code) : false;
            $data = $table_data->getData($ID);
            $data[0]['coupon_type'] = '';
            $data[0]['coupon_type_value'] = '';
            if ($data[0]['coupon_amount'] > 0) {
                $data[0]['coupon_type'] = 'fix';
                $data[0]['coupon_type_value'] = $data[0]['coupon_amount'];
            }
            if ($data[0]['coupon_percent'] > 0) {
                $data[0]['coupon_type'] = 'percent';
                $data[0]['coupon_type_value'] = $data[0]['coupon_percent'];
            }
            if ($data[0]['coupon_free_shipping'] == 1) {
                $data[0]['coupon_type'] = 'freeshipping';
                $data[0]['coupon_type_value'] = $data[0]['coupon_free_shipping'];
            }

            if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                foreach (xt_promo_configuration::$_social_networks as $social_network) {
                    $query = "SELECT coupon_id, social_network FROM " . TABLE_COUPONS_SOCIAL_NETWORKS . " 
                    WHERE coupon_id=? AND social_network = ?";
                    $rs = $db->Execute($query,array($ID,$social_network));
                    $data[0]['xt_social_' . $social_network] = (($rs->RecordCount() > 0) ? 1 : 0);
                }
            }
            $data[0]['shop_permission_info']=_getPermissionInfo();
            ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:id_data')) ? eval($plugin_code) : false;
        } else {
            $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key,'', '', $permissions);
            $data = $table_data->getHeader();
            //            $data[0]['coupon_type'] = '';
            //            $data[0]['coupon_type_value'] = '';
        }

        if ($table_data->_total_count != 0 || !$table_data->_total_count)
            $count_data = $table_data->_total_count;
        else
            $count_data = count($data);

        $obj->totalCount = $count_data;
        $obj->data = $data;

        ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_get_bottom')) ? eval($plugin_code) : false;

        return $obj;
    }

    function _set ($data, $set_type = 'edit')
    {
        global $db, $language, $filter, $xtPlugin;
        if ($set_type == 'new') {
            $data['products_id'] = (int)$this->url_data['products_id'];
            if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
                $data['coupon_created_ip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $data['coupon_created_ip'] = $_SERVER["REMOTE_ADDR"];
            }
            $data['coupon_created_date'] = date('Y-m-d H:i:s');
        }

        //  ',' durch ein '.' ersetzen
        $data['coupon_minimum_order_value'] = str_replace(',', '.', $data['coupon_minimum_order_value']);

        $data['coupon_last_modified_date'] = date('Y-m-d H:i:s');
        if ($data['coupon_type'] == 'fix') {
            $data['coupon_amount'] = str_replace(',', '.', $data['coupon_type_value']);
            $data['coupon_percent'] = 0;
            $data['coupon_free_shipping'] = 0;
            unset($data['coupon_type']);
            unset($data['coupon_type_value']);
        }
        if ($data['coupon_type'] == 'percent') {
            $data['coupon_amount'] = 0;
            if ($data['coupon_type_value'] > 100) {
                $data['coupon_type_value'] = 100;
            }
            $data['coupon_percent'] = $data['coupon_type_value'];
            $data['coupon_free_shipping'] = 0;
            unset($data['coupon_type']);
            unset($data['coupon_type_value']);
        }
        if ($data['coupon_type'] == 'freeshipping') {
            $data['coupon_amount'] = 0;
            $data['coupon_percent'] = 0;
            $data['coupon_free_shipping'] = 1;
            unset($data['coupon_type']);
            unset($data['coupon_type_value']);
        }

        if (empty($data['coupon_status'])) {
            $data['coupon_status'] = 0;
        }
		
		if (empty($data['coupon_can_decrease_shipping'])) {
            $data['coupon_can_decrease_shipping'] = 0;
        }

        // 100% coupon with zero shipping
        if (empty($data['coupon_free_on_100_status'])) {
            $data['coupon_free_on_100_status'] = 0;
        }
        ///

        if ($data['coupon_max_per_customer'] && $data['coupon_max_total'] && $data['coupon_max_per_customer'] > $data['coupon_max_total']) {
            $data['coupon_max_per_customer'] = $data['coupon_max_total'];
        }

        if (isset($xtPlugin->active_modules['xt_campaigntracking'])) {
            if (!$data['custom_reference']) {
                $data['custom_reference'] = '';
            } else {
                $disabled_words = array("a", "is", "the", "are", "etc", "as");
                $disabled_words = "{" . implode("}|{", $disabled_words) . "}";
                $str = strtolower(trim($data['custom_reference']));
                $str = preg_replace('/[^a-z0-9-]/', '-', $str);
                $str = preg_replace('/-+/', "--", $str);
                $str = preg_replace('/-[' . $disabled_words . ']+-/', '-', $str);
                $str = preg_replace('/-+/', "-", $str);
                $data['custom_reference'] = trim($str, '-');
            }
        }

        if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
            if (!isset($data['coupon_internal_status'])) {
                $query = "DELETE FROM " . TABLE_COUPONS_SOCIAL_NETWORKS . " WHERE coupon_id=?";
                $rs = $db->Execute($query,array($data['coupon_id']));
                $is_social_coupon = false;
                foreach (xt_promo_configuration::$_social_networks as $social_network) {
                    if ($data['xt_social_' . $social_network]) {
                        $o = new adminDB_DataSave(TABLE_COUPONS_SOCIAL_NETWORKS, array('coupon_id' => $data['coupon_id'], 'social_network' => $social_network));
                        $o->saveDataSet();
                        $is_social_coupon = true;
                    }
                }
                if ($is_social_coupon && !$data['coupon_code']) {
                    $data['coupon_code'] = self::generateCodeForSocialCoupon();
                }
            } else {
                $o = new adminDB_DataSave(TABLE_COUPONS, array('coupon_id' => $data['coupon_id'], 'coupon_internal_status' => 1));
                $o->saveDataSet();
            }
        }

        $obj = new stdClass();
        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $obj = $o->saveDataSet();

        $oCD = new adminDB_DataSave($this->_table_lang, $data, true, __CLASS__);
        $objCD = $oCD->saveDataSet();

        $coupon_id = $set_type == 'new' ? $obj->new_id : $data[$this->_master_key];
        $set_perm = new item_permission($this->perm_array);
        $set_perm->_saveData($data, $coupon_id);
        
        $obj->success = true;
        if ($set_type != 'new' && isset($xtPlugin->active_modules['xt_campaigntracking']) && !isset($data['coupon_internal_status'])) {
            $campaignTracking = new xt_campaigntracking('', 'xt_coupons', $data['coupon_id']);
            $campaignTracking->setCustomReference($data['custom_reference']);
        }
        return $obj;
    }

    function _unset ($id = 0)
    {
        global $db, $xtPlugin;
        if ($id == 0)
            return false;
        if ($this->position != 'admin')
            return false;
        $id = (int)$id;
        if (!is_int($id))
            return false;

        $db->Execute("DELETE FROM " . TABLE_COUPONS_PRODUCTS . " WHERE coupon_id = ?",array($id));
        $db->Execute("DELETE FROM " . TABLE_COUPONS_CATEGORIES . " WHERE coupon_id = ?",array($id));
        $db->Execute("DELETE FROM " . TABLE_COUPONS_TOKEN . " WHERE coupon_id = ?",array($id));
        $db->Execute("DELETE FROM " . TABLE_COUPONS_CUSTOMERS . " WHERE coupon_id = ?",array($id));
        $db->Execute("DELETE FROM " . $this->_table . " WHERE " . $this->_master_key . " = ?",array($id));
        $db->Execute("DELETE FROM " . TABLE_COUPONS_DESCRIPTION . " WHERE coupon_id = ?",array($id));
        if (isset($xtPlugin->active_modules['xt_campaigntracking'])) {
            $campaignTracking = new xt_campaigntracking('', 'xt_coupons', $id);
            $campaignTracking->_remove();
        }
    }

    /**
     * ermittelt die verfügbaren Coupons, z.B: für in Dropdown
     *
     */
    function getCoupons ()
    {
        global $xtPlugin, $db, $language;

        $data = array();
        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

        $db_data = $table_data->getData();

        if (is_array($db_data)) {
            foreach ($db_data as $item) {
                $tmp_data = array('id' => $item['coupon_id'], 'name' => $item['coupon_name_' . $language->code], 'desc' => $item['coupon_description_' . $language->code]);
                if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
                    $tmp_data['social_network_message'] = $item['social_network_message' . $language->code];
                }
                $data[] = $tmp_data;
            }
        }
        return $data;
    }

    function _check_coupon_avail ($coupon_code = '', $pos = 'add_coupon')
    {
        $this->_check_coupon_in_cart();
        $arr_coupon = $this->_check_coupon($coupon_code, $pos, false);
        return $arr_coupon;
    }

    /**
     * versucht einen Coupon dem Warenkorb hinzuzufügen
     *
     * @param string $coupon_code
     */
    function _addToCart ($coupon_code = '', $pos = 'add_coupon')
    {
        if (trim($coupon_code) == '') {
            return false;
        }
        $arr_coupon = $this->_check_coupon_avail($coupon_code, $pos = 'add_coupon');
        if (!is_array($arr_coupon)) {
            return false;
        }

        if (is_array($arr_coupon)) {
            $this->_set_coupon($arr_coupon);
        }

        return true;
    }

    /**
     * speichert den entsprechednen Coupon in der Sesseion und Warenkorb
     *
     * @param array $arr_coupon
     */
    function _set_coupon ($arr_coupon)
    {
        $this->_unset_coupon();
        $amount = $this->calc_coupon_amount($arr_coupon);

        if ($amount > 0) {
            $amount *= -1;
            $_amount_tax_class = $arr_coupon['coupon_tax_class'];
            if (empty($_amount_tax_class))
                $_amount_tax_class = 0;
            $coupon_data_array = array('customer_id' => $_SESSION['registered_customer'],
                'qty' => '1',
                'name' => TEXT_XT_COUPON_TITLE,
                'price' => $amount,
                'tax_class' => $_amount_tax_class, //TAX_CLASS,
                'sort_order' => 0, //TAX_SORTING,
                'type' => 'xt_coupon',
                'key_id' => $arr_coupon['coupon_id'],
                'model' => 'fix');
            $_SESSION['cart']->_addSubContent($coupon_data_array);
        }
        $arr_shipping = $_SESSION['cart']->sub_content['shipping'];

        // 100% coupon with zero shipping
        // if (is_array($arr_shipping) && $this->coupon_data['coupon_free_shipping'] == '1')
        if (is_array($arr_shipping) && ($this->coupon_data['coupon_free_shipping'] == '1' || ($this->coupon_data['coupon_percent'] == '100' && $this->coupon_data['coupon_free_on_100_status'] == '1'))) {
            $amount = $arr_shipping['products_price'];
            $amount *= -1;
            $coupon_data_array = array('customer_id' => $_SESSION['registered_customer'], 'qty' => '1', 'name' => TEXT_XT_COUPON_FREE_SHIPPING, 'price' => $amount, 'tax_class' => $arr_shipping['products_tax_class'], //TAX_CLASS,
                'sort_order' => 0, //TAX_SORTING,
                'type' => 'xt_coupon');
            $_SESSION['cart']->_addSubContent($coupon_data_array);
        }
        $_SESSION['sess_coupon'] = $arr_coupon;
        $_SESSION['cart']->_refresh();

    }

    /**
     * löscht einen evtl. vorhandenen Coupon in de Session und dem Warenkorb
     *
     */
    function _unset_coupon ()
    {
        unset($_SESSION['cart']->show_sub_content['xt_coupon']);
        unset($_SESSION['cart']->sub_content['xt_coupon']);
        unset($_SESSION['sess_coupon']);
    }

    /**
     * prüft, ob der Coupon im Warenkorb noch zulässig ist für diesen Warenkorb.
     * Wenn nicht mehr gültig, wird dieser gelöscht.
     *
     */
    function _check_coupon_in_cart ($pos = '', $cart_products = false)
    {
        global $info;

        $erg = true;
        $coupon_code = '';
        if (!isset($_SESSION['sess_coupon'])) {
            return false;
        } else {
            $cart_coupon = $_SESSION['sess_coupon'];
            if ($cart_coupon['coupon_token_code'] != '') {
                $coupon_code = $cart_coupon['coupon_token_code'];
            } else
                if ($cart_coupon['coupon_code'] != '') {
                    $coupon_code = $cart_coupon['coupon_code'];
                }
            if ($coupon_code != '') {
                $arr_coupon = $this->_check_coupon($coupon_code, $pos, $cart_products);
                if (!is_array($arr_coupon)) {
                    if(!empty($this->error_info))
                    {
                        $info->_addInfoSession($this->error_info, 'error');
                    }
                    $this->_unset_coupon();
                    $erg = false;
                } else {
                    $erg = $arr_coupon;
                }
            } else {
                $this->_unset_coupon();
                $erg = false;
            }
        }
        return $erg;
    }

    /**
     * Prüft ob ein Coupon/token auf allgemeine gültigkeit gegen den aktuellen Warenkorb
     *
     * @param string $coupon_code  zeichenfolge des Coupon
     * @return  wenn gültig:Array mit den Coupondaten, sonst false
     */
    function _check_coupon ($coupon_code = '', $pos = 'add_coupon', $cart_products)
    {
        global $db, $info, $price, $xtPlugin, $currency,$store_handler;
        if (trim($coupon_code) == '') {
            return false;
        }
        $cart = $_SESSION['cart'];
        $customer = $_SESSION['customer'];


        // coupon werte holen

        // check ob coupon vorhanden mit liste
        $add_where = '';
        $sql  = 'Select ';
        $sql .= '  c.*,';
        $sql .= '  ct.*';
        $sql .= ' FROM ';
        $sql .= TABLE_COUPONS . ' c 
                LEFT JOIN ' . TABLE_COUPONS_TOKEN . ' ct ON c.coupon_id = ct.coupon_id ';
        if($store_handler->store_count > 1)
        {
            $sql .= "LEFT JOIN " . TABLE_COUPONS_PERMISSION . " pm  ON (pm.pid = c.coupon_id and pm.pgroup = 'shop_".$store_handler->shop_id."') ";
            if(_SYSTEM_GROUP_PERMISSIONS=='blacklist'){
                $add_where = " and pm.permission IS NULL";
            }elseif(_SYSTEM_GROUP_PERMISSIONS=='whitelist'){
                $add_where = " and pm.permission = 1";
            }
        }
        $sql .= 'WHERE c.coupon_status = 1  and ct.coupon_token_code = ?';
        $sql .= ' AND (c.coupon_start_date <= now() or c.coupon_start_date is null) AND (c.coupon_expire_date >= now()  or c.coupon_expire_date is null) '
            .$add_where.'
                  LIMIT 0 , 1';
        $sql .= ' ;';

        $couponWithToken_bol = false;

        $rs = $db->Execute($sql,array($coupon_code));
        if ($rs->RecordCount() == 1) {

            $arr_coupon = $rs->fields;
            /// RESTWERT
            if ($arr_coupon['coupon_token_amount'] != 0) {
                $arr_coupon['coupon_amount'] = $arr_coupon['coupon_token_amount'];
            }
			$arr_coupon['coupon_amount'] = $price->_calcCurrency($arr_coupon['coupon_amount']);
            $couponWithToken_bol = true;
            ///
            $this->coupon_data = $arr_coupon;

        } else { // no list found, check for single coupon


            if(_SYSTEM_GROUP_PERMISSIONS=='blacklist'){
                $add_where = " and pm.permission IS NULL";
            }elseif(_SYSTEM_GROUP_PERMISSIONS=='whitelist'){
                $add_where = " and pm.permission = 1";
            }
            $add_where = '';
            $sql = '';
            $sql .= 'Select ';
            $sql .= '  c.*';
            $sql .= ' FROM ';
            $sql .= TABLE_COUPONS . ' c ';
            if($store_handler->store_count > 1)
            {
                $sql .= "left JOIN " . TABLE_COUPONS_PERMISSION . " pm  ON (pm.pid = c.coupon_id and pm.pgroup = 'shop_".$store_handler->shop_id."') ";
                if(_SYSTEM_GROUP_PERMISSIONS=='blacklist'){
                    $add_where = " and pm.permission IS NULL";
                }elseif(_SYSTEM_GROUP_PERMISSIONS=='whitelist'){
                    $add_where = " and pm.permission = 1";
                }
            }
            $sql .= ' WHERE c.coupon_status = 1 and c.coupon_code = ? ';
            // 1.3.3: making it possible to use the same code for two different customer groups
            // $sql .= ' AND (c.customers_status IS NULL OR c.customers_status = 0 OR find_in_set(?,c.customers_status))';
            $sql .= ' AND (c.coupon_start_date <= now() or c.coupon_start_date is null) AND (c.coupon_expire_date >= now()  or c.coupon_expire_date is null) '
                .$add_where.'
                  LIMIT 0 , 1';
            $sql .= ' ;';
            
            $rs = $db->Execute($sql,array($coupon_code));
            if ($rs->RecordCount() == 0) {
                $this->error_info = TEXT_COUPON_ERROR_CODE_NOT_FOUND;
                return false;
            } else {
                $arr_coupon = $rs->fields;
				$arr_coupon['coupon_amount'] = $price->_calcCurrency($arr_coupon['coupon_amount']);
                $this->coupon_data = $arr_coupon;
            }
        }

        unset($rs);
        if (count($arr_coupon) <= 0) {
            $this->error_info = TEXT_COUPON_ERROR_CODE_NOT_FOUND;
            return false;
        }

        // Mindest Bestellwert
        if (($arr_coupon['coupon_minimum_order_value'] > 0) && ($arr_coupon['coupon_minimum_order_value'] > ($cart->cart_total_full_coupons))) {
            $this->error_info = TEXT_COUPON_ERROR_MIN_ORDER_VALUE_FAILED;
            return false;
        }

        $allowed_customers_status = explode(',', $arr_coupon['customers_status']);
        // Kundengruppe prüfen
        if ( $arr_coupon['customers_status']!=0 && !in_array($customer->customers_status, $allowed_customers_status)) {
            $this->error_info = TEXT_COUPON_ERROR_CUSTOMER_GROUP_NOT_ALLOWED;
            return false;
        }

        // direkte Kundenzuordnngen prüfen
        $sql = '';
        $sql .= 'select customers_id from ' . TABLE_COUPONS_CUSTOMERS . ' where coupon_id = ?';
        $rs = $db->Execute($sql,array((int)$arr_coupon['coupon_id']));
        $arr_customers = $rs->getArray();
        unset($rs);

        if (count($arr_customers) > 0) {
            $customer_found = false;
            foreach ($arr_customers as $item) {
                if ($customer->customers_id == $item['customers_id']) {
                    $customer_found = true;
                }
            }
            if (!$customer_found) {
                $this->error_info = TEXT_COUPON_ERROR_CUSTOMER_NOT_ALLOWED;
                return false;
            }

        }

        // Produkte
        $products_in_cart = $this->_get_coupon_products_in_cart($arr_coupon['coupon_id'], $cart_products);
        if (count($products_in_cart['all']) > 0) {
            if (count($products_in_cart['found']) <= 0) {
                $this->error_info = TEXT_COUPON_ERROR_NO_MATCHED_PRODUCT;
                return false;
            }

            //check total amount of products in cart & min order value
            $sum_of_valided_products = 0;
            foreach ($products_in_cart['found'] as $k => $v) {
                $sum_of_valided_products += $v['products_price']['plain'] * $v['products_quantity'];
            }

            if ($arr_coupon['coupon_minimum_order_value'] > $sum_of_valided_products) {
                $this->error_info = TEXT_COUPON_ERROR_MIN_ORDER_VALUE_FAILED;
                return false;
            }
        }

        if ($arr_coupon['coupon_token_id'] == null) {
            // anzahl einlösungen insgesammt
            $sql = "select count(*) as count from " . TABLE_COUPONS_REDEEM . " where coupon_id = ? and ((coupon_token_id = 0) or (coupon_token_id is null))";
            $rs = $db->Execute($sql,array((int)$arr_coupon['coupon_id']));
            $total_redeemed = $rs->fields['count'];

            $total_redeemed_customer = 0;
            if ($_SESSION['customer']->customer_info['account_type'] == '0') // kunde mit passwort
            {
                $sql = "select count(*) as count from " . TABLE_COUPONS_REDEEM . " where coupon_id = ? and ((coupon_token_id = 0) or (coupon_token_id is null)) and customers_id=?";
                $rs = $db->Execute($sql, array((int)$arr_coupon['coupon_id'], $customer->customers_id));
                $total_redeemed_customer = $rs->fields['count'];
            }
            elseif ($_SESSION['registered_customer']) // gast
            {
                $sql = "SELECT COUNT(c.customers_id) as count from " . TABLE_COUPONS_REDEEM . " cr
                LEFT JOIN ".TABLE_CUSTOMERS ." c ON cr.customers_id = c.customers_id 
                WHERE coupon_id = ? and ((coupon_token_id = 0) or (coupon_token_id is null))
                and c.customers_email_address LIKE ?";
                $like_email = preg_replace('/(\+.*)@/', '%%@', $_SESSION['customer']->customer_info['customers_email_address']);
                $rs = $db->Execute($sql, [(int)$arr_coupon['coupon_id'], $like_email]);
                $total_redeemed_customer = $rs->fields['count'];
            }

            unset($rs);

            if (($arr_coupon['coupon_max_total'] > 0) && ($total_redeemed >= $arr_coupon['coupon_max_total'])) {
                $this->error_info = TEXT_COUPON_ERROR_MAX_REDEEM;
                return false;
            }

            if (($arr_coupon['coupon_max_per_customer']) && ($total_redeemed_customer >= $arr_coupon['coupon_max_per_customer'])) {
                $this->error_info = TEXT_COUPON_ERROR_MAX_CUSTOMER_REEDEM;
                return false;
            }
        }

        // Mindest Produktewert im Warenkorb
        // 1.3.1: round(x,2) added to fix rounding difference between coupon and cart amounts
        $wk_products_amount = round($this->_get_coupon_products_in_cart_amount($arr_coupon['coupon_id']), 2);

        if($arr_coupon['coupon_tax_class'] > 0)
        {
            global $tax;
            $arr_coupon['coupon_amount'] = $price->_removeTax($arr_coupon['coupon_amount'], $tax->data[$arr_coupon['coupon_tax_class']]);
        }

        $wk_amount = round($price->_BuildPrice($arr_coupon['coupon_amount'], $arr_coupon['coupon_tax_class']), 2);
        //$wk_amount_data = $price->_getPrice(array($arr_coupon['coupon_amount'], 'qty'=>1, 'tax_class'=>$arr_coupon['coupon_tax_class'], 'format'=>false, 'curr'=>true, 'format_type'=>'default'));

        //        if(($arr_coupon['coupon_amount'] > 0) && ($wk_products_amount > -1) && ($arr_coupon['coupon_amount'] >$wk_products_amount)) {
        if (($wk_amount > 0) && ($wk_products_amount > -1) && ($wk_amount > $wk_products_amount) && (XT_COUPONS_USE_LEFTOVER != 1)) {
            $this->error_info = TEXT_COUPON_ERROR_MIN_ORDER_VALUE_FAILED;
            return false;
        }

        $shipping_total = 0;
        global $page;
        $calc_shipping = $page->page_name == 'checkout' && in_array($page->page_action, array('payment', 'confirmation'))
            || $page->page_name == 'checkout' && $_POST['action'] == 'process';
        if ($calc_shipping && $arr_coupon["coupon_can_decrease_shipping"]==1) {
            //$shipping_total = $price->_BuildPrice($cart->sub_content['shipping']["products_price"], $cart->sub_content['shipping']["products_tax_class"]);
            $shipping_total_price = $price->_getPrice(array('price'=>$cart->sub_content['shipping']["products_price"], 'qty'=>1, 'tax_class'=>$cart->sub_content['shipping']["products_tax_class"], 'format'=>false, 'curr'=>true, 'format_type'=>'default'));
            $shipping_total = $shipping_total_price['plain'];
           $total_cart = round(($cart->content_total['plain'] + $shipping_total),2)  ;//adding shipping cost in total cart amount
        }
		else $total_cart = $cart->content_total['plain'];
        //$total_cart = $cart->content_total['plain'];
		
        // Mindest Warenkorbwert
        if (($wk_amount > 0) && ($wk_amount > $total_cart)) { // $cart->content_total['plain']
            /// RESTWERT
            if ($couponWithToken_bol && $arr_coupon['coupon_tax_class'] == 0 && XT_COUPONS_USE_LEFTOVER ==1) {
                $arr_coupon['coupon_amount_leftover'] = round(($wk_amount - $total_cart /*- $shipping_total*/), 2); // round ??? // $cart->content_total['plain']

                $arr_coupon['coupon_amount'] = $total_cart  /*+ $shipping_total*/;
                $this->coupon_data['coupon_amount'] = $total_cart  /*+ $shipping_total*/;
                $this->coupon_data['coupon_amount_leftover'] = $arr_coupon['coupon_amount_leftover'];
                $leftoverInfo_bol = true;
            } ///
            else {
                //$this->error_info = TEXT_COUPON_ERROR_MIN_ORDER_VALUE_FAILED;
                //return false;
            }
        }

        // Coupon_token schon verwendet ?
        if ($arr_coupon['coupon_token_order_id'] > 0) {
            $this->error_info = TEXT_COUPON_ERROR_TOKEN_USED;
            return false;
        }

        if (isset($xtPlugin->active_modules['xt_socialcommerce'])) {
            $record = $db->Execute("SELECT * FROM " . TABLE_SOCIAL_PROMOTION_ACTIVITIES . " where customers_id=?",array($customer->customers_id));
            if ($record->RecordCount() > 0) {
                unset($record);
                $this->error_info = TEXT_COUPON_ERROR_MAX_CUSTOMER_REEDEM;
                return false;
            }
            $query = "SELECT coupon_id, social_network FROM " . TABLE_COUPONS_SOCIAL_NETWORKS . " WHERE coupon_id=?";
            $rs = $db->Execute($query,array((int)$arr_coupon['coupon_id']));
            if ($rs->RecordCount() > 0 && !isset($_SESSION['xt_social_promotion_activities']->promotionActivities)) {
                unset($rs);
                $this->error_info = TEXT_COUPON_ERROR_CUSTOMER_NOT_ALLOWED;
                return false;
            }
        }

        /// RESTWERT
        if ($this->error_info == '' && $leftoverInfo_bol) {
            require_once(_SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.price.php');
            $formatedLeftover_str = $price->_StyleFormat($arr_coupon['coupon_amount_leftover']);
            $info->_addInfo(TEXT_COUPON_ADDED_LEFTOVER . " " . $formatedLeftover_str, 'info');
        }
        ///
        $_SESSION['sess_coupon'] = $arr_coupon;
        return $arr_coupon;
    }

    /**
     * ermittelt dem Coupon zugeordneten Producte. Direkt und über die Categorie
     *
     * @param string $coupon_id
     */
    function _get_products_for_coupon ($coupon_id)
    {
        global $db, $xtPlugin;

        // producte direkt
        $arr_products = Array();
        $arr_tmp = Array();
        $sql = '';
        $sql .= 'select products_id from ' . TABLE_COUPONS_PRODUCTS . ' where coupon_id =? order by products_id;';
        $rs = $db->Execute($sql,array((int)$coupon_id));
        $arr_tmp = $rs->getArray();
        unset($rs);

        foreach ($arr_tmp as $item) {
            $arr_products[] = $item['products_id'];
            ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_get_products_for_coupon_foreach')) ? eval($plugin_code) : false;
        }
        ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_get_products_for_coupon_after_foreach')) ? eval($plugin_code) : false;

        //categorien
        $arr_tmp = Array();
        $sql = '';
        $sql .= ' Select products_id  from ' . TABLE_COUPONS_CATEGORIES . ' as cc ';
        $sql .= ' Left Join ' . TABLE_PRODUCTS_TO_CATEGORIES . ' as ptc On cc.categories_id = ptc.categories_id';
        $sql .= ' where cc.coupon_id = ?';
        $sql .= ';';

        $rs = $db->Execute($sql,array((int)$coupon_id));
        $arr_tmp = $rs->getArray();
        unset($rs);
        foreach ($arr_tmp as $item) {
            $arr_products[] = $item['products_id'];
        }

        ($plugin_code = $xtPlugin->PluginCode('class.xt_coupons.php:_get_products_for_coupon_bottom')) ? eval($plugin_code) : false;

        return $arr_products;
    }

    /**
     * ermittelt die Produkte zum coupon im Warenkorb
     *
     * @param string $coupon_id
     */
    function _get_coupon_products_in_cart ($coupon_id, $cart_products = false)
    {

        static $_get_coupon_products_in_cart;
        if (isset($_get_coupon_products_in_cart))
            return $_get_coupon_products_in_cart;
        
        $cart = & $_SESSION['cart'];
        $arr_products = $this->_get_products_for_coupon($coupon_id);
        $arr_products_found = Array();
        $arr_content = $cart->content;
        if (is_array($arr_content)) {
            foreach ($arr_content as $item) {
                if(is_array($cart_products))
                {
                    $product_data = $cart_products[$item['products_id']]->data;
                }
                else
                {
                    $content_product = product::getProduct($item['products_id'], 'default', $item['products_quantity']);
                    $product_data = $content_product->data;
                }
                unset($product_data['products_quantity']);
                $item = array_merge($item, $product_data);
                if (is_array($arr_products) and (count($arr_products) > 0)) {
                    if (in_array($item['products_id'], $arr_products)) {
                        $arr_products_found[$item['products_id']] = $item;
                    }
                } else {
                    $arr_products_found[$item['products_id']] = $item;
                }
            }
        }

        $_get_coupon_products_in_cart = array('all' => $arr_products, 'found' => $arr_products_found);
        return $_get_coupon_products_in_cart;
    }

    function _get_coupon_products_in_cart_amount ($coupon_id)
    {
        static $_get_coupon_products_in_cart_amount;
        if (isset($_get_coupon_products_in_cart_amount))
            return $_get_coupon_products_in_cart_amount;
        
        $erg = -1; // zusätzliches Kennzeichen wenn keine Produkte zugeordnet sind.
        $a = $this->_get_coupon_products_in_cart($coupon_id);
        if (count($a['all']) > 0) {
            $erg = 0;
            if (is_array($a['found'])) {
                foreach ($a['found'] as $item) {
                    $erg += (int)$item['products_quantity'] * $item['products_price']['plain'];
                }
            }
        }

        $_get_coupon_products_in_cart_amount = $erg;
        return $_get_coupon_products_in_cart_amount;
    }

    /**
     * prüft den Coupon in der Session und berechnet dessen Wert neu
     *
     */
    function coupon_recalc ($pos = '')
    {
        $check = false;
        $cart = & $_SESSION['cart'];
        $arr_coupon = $this->_check_coupon_in_cart($pos);
        if (is_array($arr_coupon)) {
            $new_amount = $this->calc_coupon_amount($arr_coupon);
            $coupon_sub = $cart->sub_content['xt_coupon'];
            if ($new_amount != $coupon_sub['price']) {
                $this->_set_coupon($arr_coupon);
            }

            $arr_shipping = $_SESSION['cart']->sub_content['shipping'];

            // 100% coupon with zero shipping
            // if (is_array($arr_shipping) && $this->coupon_data['coupon_free_shipping'] == '1')
            if (is_array($arr_shipping) && ($this->coupon_data['coupon_free_shipping'] == '1' || ($this->coupon_data['coupon_percent'] == '100' && $this->coupon_data['coupon_free_on_100_status'] == '1'))) {
                //                 unset($_SESSION['cart']->show_sub_content['xt_coupon']);
                //                 unset($_SESSION['cart']->sub_content['xt_coupon']);
                $amount = $arr_shipping['products_price'];
                $amount *= -1;
                $coupon_data_array = array('customer_id' => $_SESSION['registered_customer'], 'qty' => '1', 'name' => TEXT_XT_COUPON_FREE_SHIPPING, 'price' => $amount, 'tax_class' => $arr_shipping['products_tax_class'], //TAX_CLASS,
                    'sort_order' => 0, //TAX_SORTING,
                    'type' => 'xt_coupon');
                //$_SESSION['cart']->_addSubContent($coupon_data_array);
                $_SESSION['cart']->sub_content['xt_coupon']['products_price'] = $amount;
                $_SESSION['cart']->sub_content['xt_coupon']['products_tax_class'] = $arr_shipping['products_tax_class'];
                $_SESSION['cart']->sub_content['xt_coupon']['products_name'] = TEXT_XT_COUPON_FREE_SHIPPING;
                $_SESSION['cart']->sub_content['xt_coupon']['products_key'] = 'xt_coupon';
                $_SESSION['cart']->sub_content['xt_coupon']['products_key_id'] = $arr_coupon["coupon_id"];
                $_SESSION['cart']->sub_content['xt_coupon']['products_model'] = 'freeshipping';
            }
        } else {
            $this->_unset_coupon();
        }
    }

    /**
     * berechnet den Preis für ein bestimmtes Produkt
     *
     * @param mixed $value
     */
    function coupon_product_price ($value,$not_in_session=0,$coupon='')
    {
        static $arr_tmp = array();
        $erg['plain_otax'] = $value['products_price']['plain_otax'];
        $erg['plain'] = $value['products_price']['plain'];
        $arr_products = '';
        $products_id = $value['products_id'];
        if ($this->_check_coupon_in_cart() == true){
            $continue = true;
            $arr_coupon = $_SESSION['sess_coupon'];
        }else if ($not_in_session==1){
            $arr_coupon = $coupon;
            $continue = true;
        }
        
        if ($continue) {
           
            if (is_array($arr_coupon)) {
                $coupon_id = $arr_coupon['coupon_id'];
                if (!is_array($arr_tmp["found"]) || (is_array($arr_tmp["found"]) && count($arr_tmp["found"])==0)){
                    $arr_tmp = $this->_get_coupon_products_in_cart($coupon_id);
                }
                $arr_products = array();
                if (is_array($arr_tmp)) {
                    foreach ($arr_tmp['found'] as $item) {
                        $arr_products_found[] = $item['products_id'];
                    }
                }
                if (is_array($arr_products_found)) {
                    if (in_array($products_id, $arr_products_found)) {
                        $perc = $arr_coupon['coupon_percent'];
                        if (($perc > 0) && ($perc <= 100)) {
                            $erg = array();
                            $erg['plain_otax'] = $value['products_price']['plain_otax'] * (100 - $perc) / 100;
                            $erg['plain'] = $value['products_price']['plain'] * (100 - $perc) / 100;
                        }
                    }
                }
            }
        }
        return $erg;
    }

   function coupon_product_percent ($value,$not_in_session=0,$coupon='', $cart_products = false)
    {
        static $arr_tmp = array();
        $continue = false;
        $products_id = $value['products_id'];
        if ($this->_check_coupon_in_cart('', $cart_products) == true){
            $continue = true;
            $arr_coupon = $_SESSION['sess_coupon'];
        }else if ($not_in_session==1){
            $arr_coupon = $coupon;
            $continue = true;
        }
        
        if ($continue) {
            
            if (is_array($arr_coupon)) {
                $coupon_id = $arr_coupon['coupon_id'];
                if (count($arr_tmp["found"])==0){
                    $arr_tmp = $this->_get_coupon_products_in_cart($coupon_id, $cart_products);
                }
                
                $arr_products = array();
                if (is_array($arr_tmp)) {
                    foreach ($arr_tmp['found'] as $item) {
                        $arr_products_found[] = $item['products_id'];
                    }
                }
               
                if (is_array($arr_products_found)) {
                    if (in_array($products_id, $arr_products_found)) {
                        
                        $perc = $arr_coupon['coupon_percent'];
                        //    echo $perc;
                        if (($perc > 0) && ($perc <= 100)) {
                            return $perc;
                        }
                    }
                }
            }
        }
        return 0;
    }

    /**
     * brechnet wert des coupon anhand des aktuellen Warenkorb
     *
     * @param mixed $coupon
     * @return mixed
     */
    function calc_coupon_amount ($coupon)
    {
        $erg = 0;
        if (!is_array($coupon)) {
            return 0;
        }
        $cart = & $_SESSION['cart'];
        $amount_value_fix = 0;
        $amount_value_perc = 0;

        if ($coupon['coupon_amount'] > 0) {
            $amount_value_fix = $coupon['coupon_amount'];
        }
		
        $cart->coupon_fix_discount = $amount_value_fix;
        return $amount_value_fix;
    }

    public static function getBest ($socialNetwork)
    {
        $bestCoupon = null;
        $coupons = self::getAvailableCoupons($socialNetwork);
        foreach ($coupons as $coupon) {
            if ($bestCoupon === null) {
                $bestCoupon = $coupon;
            } else {
                if ($bestCoupon['coupon_free_shipping']) {
                    if (!$coupon['coupon_free_shipping']) {
                        $bestCoupon = $coupon;
                    }
                } else {
                    if (!$coupon['coupon_free_shipping']) {
                        if ($bestCoupon['coupon_percent']) {
                            if ($coupon['coupon_percent']) {
                                if ($bestCoupon['coupon_percent'] < $coupon['coupon_percent']) {
                                    $bestCoupon = $coupon;
                                }
                            } else {
                                $bestCoupon = $coupon;
                            }
                        } else {
                            if (!$coupon['coupon_percent']) {
                                if ($bestCoupon['coupon_amount'] < $coupon['coupon_amount']) {
                                    $bestCoupon = $coupon;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $bestCoupon;
    }

    public static function getAvailableCoupons ($socialNetwork)
    {
        global $db;
        $query = "select distinct c.* from " . TABLE_COUPONS . " c, " . TABLE_COUPONS_SOCIAL_NETWORKS . " c_net 
                  where c.coupon_internal_status=0  and c.coupon_status = 1
    				  AND (c.coupon_start_date <= now() OR c.coupon_start_date is null)
    				  AND (c.coupon_expire_date >= now() OR c.coupon_expire_date is null)
    				  AND c_net.coupon_id = c.coupon_id
    				  AND c_net.social_network =?";
        $rs = $db->Execute($query,array($socialNetwork));
        if ($rs->RecordCount() == 0) {
            return false;
        } else {
            return $rs->getArray();
        }
    }

    public static function generateCodeForSocialCoupon ($length = 8)
    {
        $chars = "1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 1;
        $password = "";
        while ($i <= $length) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
            $i++;
        }
        return $password;
    }

    function coupons_get_saved_customers_status($data)
    {
        global $db, $customers_status;

        $edit_id = $this->url_data['id'];
        $obj = new stdClass();
        $obj->topics = array();
        $obj->totalCount = 0;

        $settings = false;
        if (!empty($edit_id))
        {
            $query = "SELECT * FROM " . TABLE_COUPONS . " WHERE coupon_id=?";
            $record = $db->Execute($query, array($edit_id));
            if ($record->RecordCount() == 1)
            {
                $settings =  $record->fields;
            }
            $record->Close();
        }

        if ($settings)
        {
            $customers_array = explode(',', $settings['customers_status']);

            if (!empty($customers_array)) {

                foreach ($customers_array as $id) {
                    if($id==0)
                    {
                        //$obj->topics[] = array('id' => 0, 'name' => TEXT_EMPTY_SELECTION, 'desc' => '');
                    }
                    else $obj->topics[] = array('id' => $id, 'name' => $customers_status->getGroupName($id), 'desc' => '');
                }
            }
            else {
                //$obj->topics[] = array('id' => 0, 'name' => TEXT_ALL, 'desc' => '');
            }
        }

        $obj->totalCount = count($obj->topics);
        return json_encode($obj);
    }

}
