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

include_once _SRV_WEBROOT.'xtFramework/library/phpxml/xml.php';

class getAdminDropdownData {

	function getSystemstatusDropdown($position) {
		global $system_status;
		$data=array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			$status_data = $system_status->values[$position];
	
			if (is_array($status_data)) {
				$data = array(array('id' => '', 'name' => TEXT_EMPTY_SELECTION));
				foreach ($status_data as $key => $val) {
					$data[] = array('id'=>$val['id'],'name'=>$val['name']);
	
				}
				return $data;
			} else {
				
                $status_data[] =  array('id' => '',
                             'name' => TEXT_EMPTY_SELECTION);
                
                return $status_data;
			}

		}else{
			return $data;
		}
	}

	function getLanguageCodes() {
		global $db,$language,$filter;
		$data=array();

		$data[] =  array('id' => '', 'name' => TEXT_EMPTY_SELECTION);
	
        foreach ($language->_getLanguageList() as $key => $val)
        {
            $data[] =  array('id' => $val['id'], 'name' => $val['name'].'('.$val['id'].')');
        }

		return $data;

	}

	function getSslOptions() {
		$data=array();

		$data[] =  array('id' => '0',
                             'name' => 'no ssl');
		$data[] =  array('id' => '1',
                             'name' => 'ssl');

		return $data;
	}

	function getCurrencies() {
		global $db,$currency,$filter;
		$data=array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($currency->_getCurrencyList('admin') as $key => $val) {
				$data[] =  array('id' => $val['id'],
	                             'name' => $val['text']);
			}
		
		}
		
		return $data;

	}

	function getCountries() {
		global $db,$filter;
		$data=array();
		
		$countries = new countries();
		$data[] =  array(
			'id' => '',
			'name' => TEXT_EMPTY_SELECTION
		);
		
		foreach ($countries->countries_list_sorted as $key => $val) {
			$data[] =  array(
				'id' => $val['id'],
				'name' => $val['text']
			);
		}
		
		return $data;

	}

	function getLanguageClasses() {
		$data=array();
		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION,
                         'desc' => '');

		$data[] =  array('id' => 'store',
                             'name' => 'store');
		$data[] =  array('id' => 'admin',
                             'name' => 'admin');
		$data[] =  array('id' => 'both',
                             'name' => 'both');
		return $data;
	}

	public function getWYSIWYG()
	{
		return array(
			array('id' => 'ckeditor', 'name' => 'CKEditor'),
			array('id' => 'none', 'name' => TEXT_NO_WYSIWYG),
			array('id' => 'SimpleHtmlEditor', 'name' => TEXT_SIMPLE_WYSIWYG),
			//array('id' => 'TinyMce', 'name' => TEXT_TINY_WYSIWYG)
		);
	}

	function getUploadType() {
		$data=array();
		$data[] =  array('id' => 'simple_upload',
                         'name' => TEXT_SIMPLE_UPLOAD);

		$data[] =  array('id' => 'flash_upload_10',
                             'name' => TEXT_FLASH_UPLOAD,
                             'desc' => TEXT_DESC_FLASH_10);

		$data[] =  array('id' => 'flash_upload_9',
                             'name' => TEXT_FLASH_UPLOAD_OLD,
                             'desc' => TEXT_DESC_FLASH_9);
		return $data;
	}

	function getSimplePrefix() {
		$data=array();
		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);

		$data[] =  array('id' => '+',
                             'name' => '+');
		$data[] =  array('id' => '-',
                             'name' => '-');
		return $data;
	}

	function getAdvancedPrefix() {
		$data=array();
		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);

		$data[] =  array('id' => '+',
                             'name' => '+');
		$data[] =  array('id' => '-',
                             'name' => '-');
		$data[] =  array('id' => '*',
                             'name' => 'x');
		$data[] =  array('id' => '/',
                             'name' => '/');
		$data[] =  array('id' => '%',
                             'name' => '%');
		return $data;
	}

	function getFieldTypes() {
		$data=array();
		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);

		$data[] =  array('id' => 'select',
                             'name' => 'Select Field');

		$data[] =  array('id' => 'radio',
                             'name' => 'Radio Field');

		$data[] =  array('id' => 'checkbox',
                             'name' => 'Checkbox Field');		
		
		$data[] =  array('id' => 'text',
                             'name' => 'Input Field');

		$data[] =  array('id' => 'textarea',
                             'name' => 'Text Field');

		return $data;
	}


	function getLanguageNonDefines() {
		$data=array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			if ($_SESSION['debug']) {
				//            $_SESSION['debug'] = array_unique($_SESSION['debug']);
				foreach ($_SESSION['debug'] as $key => $val) {
					if ($val['desc'])
					$desc = '<br />'.__define('TEXT_NO_TEXT_LANGUAGES').': '.$val['desc'];
					if ($val)
					$data[] =  array('id' => $val['name'],
	                             'name' => $val['name'],
	                             'desc' => __define('TEXT_INFO_NONDEFINE').   $desc);
	
				}
			}
		
		}
		
		return $data;
	}

	function getShippingType() {
		$data=array();
		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);

		$data[] =  array('id' => 'item',
                             'name' => 'item');
		$data[] =  array('id' => 'price',
                             'name' => 'price');
		$data[] =  array('id' => 'weight',
                             'name' => 'weight');
		return $data;
	}

	function getGender() {
		$data=array();
		$data[] =  array('id' => 'm',
                             'name' => TEXT_MALE);
		$data[] =  array('id' => 'f',
                             'name' => TEXT_FEMALE);
		$data[] =  array('id' => 'c',
                             'name' => TEXT_COMPANY);
		return $data;
	}

	function getTrueFalse () {
		$data=array();
		$data[] =  array('id' => '1',
                             'name' => __define('TEXT_TRUE'),
                             'desc' => __define('DESC_TRUE'));
		$data[] =  array('id' => '0',
                             'name' => __define('TEXT_FALSE'),
                             'desc' => __define('DESC_FALSE'));
		return $data;
	}
    
    function getLiveTest() {
        $data=array();
        $data[] =  array('id' => 'live',
                             'name' => __define('TEXT_MODE_LIVE'),
                             'desc' => __define('DESC_MODE_LIVE'));
        $data[] =  array('id' => 'sandbox',
                             'name' => __define('TEXT_MODE_SANDBOX'),
                             'desc' => __define('DESC_MODE_SANDBOX'));
        return $data;  
    }

	function getImageClasses () {
		global $xtPlugin;
		
		$data=array();
		$data[] =  array('id' => 'default',
                             'name' => __define('TEXT_DEFAULT_IMAGE'),
                             'desc' => __define('DESC_DEFAULT_IMAGE'));
		$data[] =  array('id' => 'product',
                             'name' => __define('TEXT_PRODUCT_IMAGE'),
                             'desc' => __define('DESC_PRODUCT_IMAGE'));
		$data[] =  array('id' => 'category', // old categorie
                             'name' => __define('TEXT_CAT_IMAGE'),
                             'desc' => __define('DESC_CAT_IMAGE'));
		$data[] =  array('id' => 'manufacturer', // old manufacturers
                             'name' => __define('TEXT_MANUFACTURER_IMAGE'),
                             'desc' => __define('DESC_MANUFACTURER_IMAGE'));
		$data[] =  array('id' => 'content', // old manufacturers
                             'name' => __define('TEXT_CONTENT_IMAGE'),
                             'desc' => __define('DESC_CONTENT_IMAGE'));		
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getImageClasses')) ? eval($plugin_code) : false;
		
		return $data;
	}

	function getImageTypes () {
		global $xtPlugin;
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.ImageTypes.php';
		$it = new ImageTypes();
		$it->url_data[get_data] = 1;
		$it->position = 'admin';
		$it = $it->_get();
		
		$data=array();
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
		foreach ($it->data as $k => $v) {
			$data[] = array ('id' => $v[folder],
				'name' => $v[folder],
				'desc' => $v[height] . 'x' . $v[width]);
		}
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getImageTypes')) ? eval($plugin_code) : false;
		
		return $data;
	}


	function getDownloadStatus () {
		$data=array();
		$data[] =  array('id' => 'free',
                             'name' => __define('TEXT_FREE_DOWNLOAD'),
                             'desc' => __define('DESC_FREE_DOWNLOAD'));
		$data[] =  array('id' => 'order',
                             'name' => __define('TEXT_ORDER_DOWNLOAD'),
                             'desc' => __define('DESC_ORDER_DOWNLOAD'));
		return $data;
	}

	function getFileTypes () {
		$data=array();
		$data[] =  array('id' => 'images',
                             'name' => __define('TEXT_IMAGES'),
                             'desc' => __define('DESC_IMAGES'));
		$data[] =  array('id' => 'files',
                             'name' => __define('TEXT_FILES'),
                             'desc' => __define('DESC_FILES'));
		return $data;
	}

	function getConfTrueFalse () {
		$data=array();
		$data[] =  array('id' => 'true',
                             'name' => __define('TEXT_TRUE'),
                             'desc' => __define('DESC_TRUE'));
		$data[] =  array('id' => 'false',
                             'name' => __define('TEXT_FALSE'),
                             'desc' => __define('DESC_FALSE'));
		return $data;
	}

	function getAscDesc () {
		$data=array();
		$data[] =  array('id' => 'ASC',
                             'name' => __define('TEXT_ASC'),
                             'desc' => __define('DESC_ASC'));
		$data[] =  array('id' => 'DESC',
                             'name' => __define('TEXT_DESC'),
                             'desc' => __define('DESC_DESC'));
		return $data;
	}

	function getAdminPerm () {
		$data=array();

		$data[] =  array('id' => 'blacklist',
                                 'name' => __define('TEXT_BLACKLIST'));

		$data[] =  array('id' => 'whitelist',
                            	 'name' => __define('TEXT_WHITELIST'));
		return $data;
	}

	function getAdminRights () {
		$data=array();

		$data[] =  array('id' => 'db',
                                 'name' => __define('TEXT_DB_RIGHTS'));

		$data[] =  array('id' => 'session',
                            	 'name' => __define('TEXT_SESSION_RIGHTS'));
		return $data;
	}

	function getAddressTypes () {

		$data=array();

		$data[] =  array('id' => 'default',
                             'name' => __define('TEXT_DEFAULT_ADDRESS'),
                             'desc' => __define('DESC_DEFAULT_ADDRESS'));
		$data[] =  array('id' => 'shipping',
                             'name' => __define('TEXT_SHIPPING_ADDRESS'),
                             'desc' => __define('DESC_SHIPPING_ADDRESS'));
		$data[] =  array('id' => 'payment',
                             'name' => __define('TEXT_PAYMENT_ADDRESS'),
                             'desc' => __define('DESC_PAYMENT_ADDRESS'));
		return $data;
	}

	function getCategorySort() {

		$data=array();

		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);

		$data[] =  array('id' => 'sort',
                             'name' => __define('TEXT_SORTORDER'));
		/*
		$data[] =  array('id' => 'model',
                             'name' => __define('TEXT_PRODUCTS_MODEL'),
                             'desc' => __define('DESC_PRODUCTS_MODEL'));
*/
		$data[] =  array('id' => 'name',
                             'name' => __define('TEXT_PRODUCTS_NAME'));
		$data[] =  array('id' => 'price',
                             'name' => __define('TEXT_PRODUCTS_PRICE'));

		$data[] =  array('id' => 'date',
                             'name' => __define('TEXT_DATE_ADDED'));
		$data[] =  array('id' => 'order',
                             'name' => __define('TEXT_PRODUCTS_ORDERED'));
        $data[] =  array('id' => 'products_model',
            'name' => __define('TEXT_PRODUCTS_MODEL'));
		return $data;


	}
	
	function getManufacturersSort() {
	
		$data=array();
	
		$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
		$data[] =  array('id' => 'sort',
	                             'name' => __define('TEXT_SORTORDER'));

		$data[] =  array('id' => 'name',
	                             'name' => __define('TEXT_PRODUCTS_NAME'));
		$data[] =  array('id' => 'price',
	                             'name' => __define('TEXT_PRODUCTS_PRICE'));
	
		$data[] =  array('id' => 'date',
	                             'name' => __define('TEXT_DATE_ADDED'));
		$data[] =  array('id' => 'order',
	                             'name' => __define('TEXT_PRODUCTS_ORDERED'));
		return $data;
	
	
	}	

	function getManufacturers () {
		
		//$data=array(array('id'=>1,'name'=>'test'));
	        $data = array();	
		if(is_array($_POST) && array_key_exists('query', $_POST))
                {
		
			$m = new manufacturer();
			
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			$_data = $m->getManufacturerList('admin');
			foreach ($_data as $mdata) {
				$data[] =  array('id' => $mdata['manufacturers_id'],
	                             'name' => $mdata['manufacturers_name']);
	
			}
		
		}
		return $data;
		
	}
	
	function getStatusProduct () {
		$data = array();	
		$data[]=array('id'=>'','name'=>TEXT_ALL_PRODUCTS);
		$data[]=array('id'=>'1','name'=>TEXT_ENABLED_PRODUCTS);
		$data[]=array('id'=>'-1','name'=>TEXT_DISABLED_PRODUCTS);
		return $data;
	}
	
	function getCategoryCustomLinkType () {
		$data = array();	
		$data[]=array('id' => '0','name' => TEXT_EMPTY_SELECTION);
		$data[]=array('id'=>'product','name'=>TEXT_CATEGORY_CUSTOM_LINK_PRODUCT);
		$data[]=array('id'=>'category','name'=>TEXT_CATEGORY_CUSTOM_LINK_CATEGORY);
		$data[]=array('id'=>'content','name'=>TEXT_CATEGORY_CUSTOM_LINK_CONTENT);
		$data[]=array('id'=>'custom','name'=>TEXT_CATEGORY_CUSTOM_LINK_CUSTOM_LINK);
		$data[]=array('id'=>'plugin','name'=>TEXT_CATEGORY_CUSTOM_LINK_PLUGIN_PAGE);
		return $data;
	}
	
	function getSeoUrlLinkType () {
		$data = array();	
		$data[]=array('id' => '0','name' => TEXT_EMPTY_SELECTION);
		$data[]=array('id'=>'1','name'=>TEXT_PRODUCT);
		$data[]=array('id'=>'2','name'=>TEXT_CATEGORY);
		$data[]=array('id'=>'3','name'=>TEXT_CONTENT);
		$data[]=array('id'=>'2','name'=>TEXT_CUSTOM_LINK);
		$data[]=array('id'=>'4','name'=>TEXT_MANUFACTURER);
		$data[]=array('id'=>'1000','name'=>TEXT_PLUGIN_PAGE);
		return $data;
	}
	
	function getMasterSlaveProduct () {
		$data = array();	
		$data[]=array('id' => '0','name' => TEXT_EMPTY_SELECTION);
		$data[]=array('id'=> '1','name'=> TEXT_FILTER_MASTER_PRODUCTS);
		$data[]=array('id'=> '2','name'=> TEXT_FILTER_SLAVE_PRODUCTS);
		$data[]=array('id'=> '3','name'=> TEXT_FILTER_MASTER_SLAVE_PRODUCTS);
		return $data;
	}
	
	function getCatTree () {

		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.category.php';
			$c = new category();			
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);		
			
			$_data = $c->getAllCategoriesList ($data_array = '', $parent_id = '0', $spacer = ' ');
			foreach ($_data as $cdata) {
				$data[] =  array('id' => $cdata['categories_id'],
	                             'name' => $cdata['categories_name']);
	
			}
		
		}

		return $data;
	}

	function getCustomersStatus() {
		
		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			global $customers_status;
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			$list = $customers_status->_getStatusList('admin');
	
			foreach ($list as $ldata) {
				$data[] =  array('id' => $ldata['id'],
	                             'name' => $ldata['text']);
			}
		
		}
		
		return $data;


	}

    /**
    * get Tax Zones
    * 
    */
	function getTaxZones(){

		$data = array();
		
			if(is_array($_POST) && array_key_exists('query', $_POST)){
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.system_status.php';
			$s_status = new system_status();
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			$_data = $s_status->values['zone'];
	
			foreach ($_data as $zdata) {
				$data[] =  array('id' => $zdata['id'],
	                             'name' => $zdata['name']);
			}
		
		}
		return $data;
	}
    
    /**
    * get Tax + Shipping zones
    * 
    */
    function getTaxShippingZones(){
        global $db;
        
        $data = array();
        
            if(is_array($_POST) && array_key_exists('query', $_POST)){
            require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.system_status.php';
            $s_status = new system_status();
            $data[] =  array('id' => '',
                             'name' => TEXT_EMPTY_SELECTION);
    
            $_data = $s_status->values['zone'];
    
            foreach ($_data as $zdata) {
                $data[] =  array('id' => $zdata['id'],
                                 'name' => $zdata['name']);
            }
            $rs = $db->Execute("SELECT zone_name,zone_id FROM ".TABLE_SHIPPING_ZONES);
            if ($rs->RecordCount()>0) {
               while (!$rs->EOF) {
                   $data[] =  array('id' => '9999'.$rs->fields['zone_id'],
                                 'name' => $rs->fields['zone_name'].' ('.TEXT_SHIPPING_ZONE.')');
                   $rs->MoveNext();
               } 
            }
            // query for shipping zones
        
        }
        return $data;
    }

	function getShippingTime(){
		
		$data = array();

		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.system_status.php';
			$s_status = new system_status();
	
			$_data = $s_status->values['shipping_status'];
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($_data as $zdata) {
				$data[] =  array('id' => $zdata['id'],
	                             'name' => $zdata['name']);
			}
		
		}
		return $data;


	}
	
	function getShippingMethods() {
		global $db,$language;
		
		$rs = $db->Execute("SELECT * FROM ".TABLE_SHIPPING." s, ".TABLE_SHIPPING_DESCRIPTION." sd WHERE s.shipping_id=sd.shipping_id and sd.language_code='".$language->code."'");
		$data = array();
		if ($rs->RecordCount() > 0) {
			while (!$rs->EOF)
			{
				$data[] =  array('id' => $rs->fields['shipping_code'],
	                         'name' =>  $rs->fields['shipping_name']);
				$rs->MoveNext();
			}
		}
		return $data;	
	} 
	
	function getShippingMethodsCodes() {
		global $db,$language;

		$rs = $db->Execute("SELECT * FROM ".TABLE_SHIPPING." s, ".TABLE_SHIPPING_DESCRIPTION." sd WHERE s.shipping_id=sd.shipping_id and sd.language_code='".$language->code."'");
		$data = array();
		if ($rs->RecordCount() > 0) {
			while (!$rs->EOF)
			{
				$data[] =  array('id' => $rs->fields['shipping_code'],
					'name' =>  $rs->fields['shipping_code'],
                    'desc' => $rs->fields['shipping_name'] . ' - id:' . $rs->fields['shipping_id']
                );
				$rs->MoveNext();
			}
		}
		return $data;
	}

	function getPaymentMethods() {
		global $db,$language;
		
		$rs = $db->Execute("SELECT distinct p.payment_code, p.payment_id  FROM ".TABLE_PAYMENT." p, ".TABLE_PAYMENT_DESCRIPTION." pd WHERE p.payment_id=pd.payment_id and pd.language_code='".$language->code."' AND pd.payment_description_store_id = 1");
		$data = array();
		if ($rs->RecordCount() > 0) {
			while (!$rs->EOF)
			{
				$data[] =  array('id' => $rs->fields['payment_id'],
	                         'name' =>  $rs->fields['payment_code'],
                             'desc' => $rs->fields['payment_name'] . ' - id:' . $rs->fields['payment_id']);
				$rs->MoveNext();
			}
		}
		return $data;	
	}

	function getPaymentMethodsCodes() {
		global $db,$language, $xtPlugin;

		$rs = $db->Execute("SELECT distinct p.payment_code, payment_name, p.payment_id FROM ".TABLE_PAYMENT." p 
		LEFT JOIN ".TABLE_PAYMENT_DESCRIPTION." pd ON pd.payment_id = p.payment_id 
		WHERE pd.language_code='".$language->code."'
		AND pd.payment_description_store_id = 1");
		$data = array();
		if ($rs->RecordCount() > 0) {
			while (!$rs->EOF)
			{
                    $data[] = array('id' => $rs->fields['payment_code'],
                        'name' => $rs->fields['payment_code'],
                        'desc' => $rs->fields['payment_name'] . ' - id:' . $rs->fields['payment_id']
                    );

				$rs->MoveNext();
			}
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getPaymentMethodsCodes')) ? eval($plugin_code) : false;

		return $data;
	}

	function getOrderStatus(){

		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.system_status.php';
			$s_status = new system_status();
	
			$_data = $s_status->values['order_status'];
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($_data as $zdata) {
				$data[] =  array('id' => $zdata['id'],
	                             'name' => $zdata['name']);
			}
		
		}
		return $data;


	}

	function getPermissionAreas(){

		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.acl_area.php';
			$area = new acl_area();
			$_data = $area->_getAreaList();
	
			foreach ($_data as $adata) {
				$data[] =  array('id' => $adata['area_id'],
	                             'name' => $adata['area_name'],
	                             'desc' => $adata['area_description']);
			}
		}
		
		return $data;


	}

	function getACLGroupList(){

		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.acl_groups.php';
			$group = new acl_groups();
	
			$_data = $group->_getGroupList();
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($_data as $gdata) {
				$data[] =  array('id' => $gdata['group_id'],
	                             'name' => $gdata['name']);
			}
		}
		return $data;


	}

	function getTaxClasses(){

		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.tax_class.php';
			$t = new tax_class();
			$_data = $t->_getTaxClassList();
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($_data as $tdata) {
				$data[] =  array('id' => $tdata['tax_class_id'],
	                             'name' => $tdata['tax_class_title']);
			}
		}
		return $data;

	}

	function getTaxRatesCalculationBase(){

		$data = array(
			array(
				'id' => 'b2c_eu',
				'name' => TEXT_TAX_CALCULATION_BASE_B2C_EU),
			array(
				'id' => 'shipping_address',
				'name' => TEXT_TAX_CALCULATION_BASE_SHIPPING_ADDRESS),
			array(
				'id' => 'payment_address',
				'name' => TEXT_TAX_CALCULATION_BASE_PAYMENT_ADDRESS)
		);

		return $data;
	}

    function getProductsConditions()
    {
        $data =  [
            ['id' => '','name' => __define('TEXT_EMPTY_SELECTION')],
            ['id' => 'NewCondition','name' => __define('TEXT_PRODUCTS_CONDITION_NEW')],
            ['id' => 'UsedCondition','name' => __define('TEXT_PRODUCTS_CONDITION_USED')],
            ['id' => 'RefurbishedCondition','name' => __define('TEXT_PRODUCTS_CONDITION_REFURBISHED')],
            ['id' => 'DamagedCondition','name' => __define('TEXT_PRODUCTS_CONDITION_DAMAGED')],
        ];

        return $data;
    }

    function getProductsAvailability()
    {
        $data =  [
            ['id' => '','name' => __define('TEXT_EMPTY_SELECTION')],
            ['id' => 'Discontinued','name' => __define('TEXT_PRODUCTS_AVAILABILITY_DISCONTINUED')],
            ['id' => 'InStock','name' => __define('TEXT_PRODUCTS_AVAILABILITY_INSTOCK')],
            ['id' => 'InStoreOnly','name' => __define('TEXT_PRODUCTS_AVAILABILITY_INSTOREONLY')],
            ['id' => 'LimitedAvailability','name' => __define('TEXT_PRODUCTS_AVAILABILITY_LIMITEDAVAILABILITY')],
            ['id' => 'OnlineOnly','name' => __define('TEXT_PRODUCTS_AVAILABILITY_ONLINEONLY')],
            ['id' => 'OutOfStock','name' => __define('TEXT_PRODUCTS_AVAILABILITY_OUTOFSTOCK')],
            ['id' => 'PreOrder','name' => __define('TEXT_PRODUCTS_AVAILABILITY_PREORDER')],
            ['id' => 'PreSale','name' => __define('TEXT_PRODUCTS_AVAILABILITY_PRESALE')],
            ['id' => 'SoldOut','name' => __define('TEXT_PRODUCTS_AVAILABILITY_SOLDOUT')],
        ];

        return $data;
    }

	function getStores($current_store_id){
		global $store_handler, $xtPlugin;
		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			$_data = $store_handler->getStores();
			
			($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getStores')) ? eval($plugin_code) : false;
			
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($_data as $sdata) {
			    if (($current_store_id!='') && ($current_store_id==$sdata['id'])){
			        continue;
			    }else{
			        $data[] =  array('id' => $sdata['id'],
                                 'name' => $sdata['text']);
			    }
				
			}
		
		}
		
		return $data;

	}

	function getSortDefaults () {
		$data=array();
					
		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);

		for ($i=0; $i <= 10; $i++) {
			$data[] =  array('id' => $i*10,
                             'name' => $i*10);
		}
		return $data;
	}

	function getTemplateSets() {
		$data=array();
		
		$data[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);		
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){				
			if ($dir = opendir(_SRV_WEBROOT.'templates/')) {
	
				while (($tpl = readdir($dir)) !== false) {
					if (is_dir(_SRV_WEBROOT.'templates/'.$tpl) and (strstr($tpl, '.')==false) and (strstr($tpl, '__')==false) and ($tpl != ".") and ($tpl != "..")) {
						$data[] =  array('id' => $tpl,
			                             'name' => $tpl);
					}
				}
	
			closedir($dir);
			}
		}
		return $data;
	}

	function _getTemplateFileList ($path, $add_default = true, $plugin_path = false,$filetype = 'html', $store_id = false) {
		global $db;
		
		$files=array();
		$file_default = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST))
		{
			$dirs = array();
			if($plugin_path==false)
			{
			    $dirs[_SYSTEM_TEMPLATE] = _SRV_WEBROOT.'templates/'._SYSTEM_TEMPLATE.'/'.$path;
			    if($store_id)
                {
                    $tableExists = $db->GetOne("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='" . _SYSTEM_DATABASE_DATABASE . "' AND TABLE_NAME='" . TABLE_CONFIGURATION_MULTI.$store_id . "'");
                    if($tableExists)
                    {
                        $store_template = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store_id." WHERE config_key='_STORE_DEFAULT_TEMPLATE'");
                        if(!empty($store_template) && $store_template != _SYSTEM_TEMPLATE)
                        {
                            $dirs[$store_template] = _SRV_WEBROOT.'templates/'.$store_template.'/'.$path;
                        }
                    }
                }
			}else{
                $dirs['plugins'] = _SRV_WEBROOT.'plugins/'.$path;
			}
	
			//		if (!is_dir($dir)) return $path;
            foreach($dirs as $dir_name => $dir)
            {
                if(is_dir($dir))
                {
			$d = dir($dir);
                    while ($name = $d->read())
                    {
                        if (!preg_match('/\.(' . $filetype . ')$/', $name))
                        {
                            continue;
                        }
				
                        //$size = filesize($dir . $name);
                        //$lastmod = filemtime($dir . $name);
				$files[] = array('id' => $name,
                            'name' => $dir_name.' / '.$name);
			}
			$d->close();
                }
            }
			if (!$add_default) return $files;
	
			// add default (value is empty)
			$file_default[]= array('id' => '',
		                     'name'=> __define('TEXT_DEFAULT_TEMPLATE'));
		}
		
			return array_merge($file_default, $files);
		
	}
	
	
	function getLanguageXML () {
		$path = 'media/lang/';
		
	
			$dir = _SRV_WEBROOT.$path;
			
	
			//		if (!is_dir($dir)) return $path;
			$d = dir($dir);
			while($name = $d->read()){
				if(!preg_match('/\.(xml)$/', $name)) continue;
				
				if (!strstr($name,'_content')) {
					
				//$xml = $this->xmlToArray($dir.$name);
					
				$xml = file_get_contents($dir.$name);
				$xml = XML_unserialize($xml);	
				//__debug($xml);
				
				$size = filesize($dir.$name);
				$lastmod = filemtime($dir.$name);
				$files[] = array('id' => $name,
			                     'name'=>$xml['xtcommerce_language']['name']. ' ('.$xml['xtcommerce_language']['code'].')');
				}
			}
			$d->close();
			if (!$add_default) return $files;

	}

	function getProductTemplate () {
		$path = 'xtCore/pages/product/';
		return $this->_getTemplateFileList($path);
	}

	function getProductOptionTemplate ($path, $add_default, $plugin_path) {
		return $this->_getTemplateFileList($path, $add_default, $plugin_path);
	}
	function getProductOptionListTemplate ($path, $add_default, $plugin_path) {
		return $this->_getTemplateFileList($path, $add_default, $plugin_path);
	}
	function getProductListTemplate () {
		$path = 'xtCore/pages/product_list/';
		return $this->_getTemplateFileList($path);
	}

	function getMicropages() {
		$path = 'media/content/';
		return $this->_getTemplateFileList($path);
	}


	function getProductListingTemplate ($store_id) {
		$path = 'xtCore/pages/product_listing/';
		return $this->_getTemplateFileList($path, true, false, 'html', $store_id);
	}
	function getCategoryTemplate($store_id) {
		$path = 'xtCore/pages/categorie_listing/';
		return $this->_getTemplateFileList($path, true, false, 'html', $store_id);

	}

	function getContentHooks() {
		
		$data = array();

		if(is_array($_POST) && array_key_exists('query', $_POST)){		
		
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.content.php';
			$c = new content();
			$_data = $c->getSystemHooks();
			$data=array();
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($_data as $cdata) {
				$data[] =  array('id' => $cdata['id'],
	                             'name' => $cdata['text']);
			}
		
		}
		
		return $data;

	}

	function getContentForms() {

		$files = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			$dir = _SRV_WEBROOT.'xtCore/forms/';

			$files[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
	
			//		if (!is_dir($dir)) return $path;
			$d = dir($dir);
			while($name = $d->read()){
				if(!preg_match('/\.(php)$/', $name)) continue;
				$size = filesize($dir.$name);
				$lastmod = filemtime($dir.$name);
				$files[] = array('id' => $name,
			                     'name'=>$name);
			}
		
		}
		
		return $files;

	}

	function getStoreLogo() {
		$dir = _SRV_WEBROOT.'media/logo/';
		$d = dir($dir);
		$data=array();
		$files[] =  array('id' => '',
                         'name' => TEXT_EMPTY_SELECTION);

		while($name = $d->read()){
			if(!preg_match('/\.(gif|jpg|png)$/', $name)) continue;
			$size = filesize($dir.$name);
			$lastmod = filemtime($dir.$name);
			$files[] = array('id' => $name,
		                     'name'=>$name);
		}
		return $files;

	}

	function getContentList() {

		$data = array();
		
		if(is_array($_POST) && array_key_exists('query', $_POST)){
		
			require_once _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.content.php';
			$c = new content();
			$_data = $c->contentList();
			$data[] =  array('id' => '',
	                         'name' => TEXT_EMPTY_SELECTION);
	
			foreach ($_data as $cdata) {
				$data[] =  array('id' => $cdata['id'],
	                             'name' => $cdata['text']);
			}
		
		}
		return $data;

	}

	function matrixSort(&$matrix,$sortKey,$sort = 'ASC') {
		if (count($matrix) == 0) return false;

		foreach($matrix as $key => $subMatrix) {
			$tmpArray[$key]=$subMatrix[$sortKey];
		}
		arsort($tmpArray);

		foreach($tmpArray as $key => $value) {
			$ArrayNew[$key]=$matrix[$key];
		}

		if ($sort != 'ASC') {
			$ArrayNew = array_reverse($ArrayNew);
		}

		return $ArrayNew;

	}

	function getMailTypes() {
		$data=array();
		$data[] =  array('id' => 'smtp',
                             'name' => __define('TEXT_MAILTYPE_SMTP'));
		$data[] =  array('id' => 'mail',
                             'name' => __define('TEXT_MAILTYPE_MAIL'));
		$data[] =  array('id' => 'sendmail',
                             'name' => __define('TEXT_MAILTYPE_SENDMAIL'));
		return $data;

	}
    
    function getPaymentCostTypes() {
        
       $data=array();
        $data[] =  array('id' => '0',
                             'name' => TEXT_EMPTY_SELECTION);
        $data[] =  array('id' => '1',
                             'name' => __define('TEXT_PAYMENT_COST_DISCOUNT_PERCENT'));
        $data[] =  array('id' => '2',
                             'name' => __define('TEXT_PAYMENT_COST_ADD'));
        $data[] =  array('id' => '3',
                             'name' => __define('TEXT_PAYMENT_COST_ADD_PERCENT'));
        return $data; 
        
        
    }
    
    function getExportTpls(){

        $export_tpls_path = 'https://addons.xt-commerce.com/psm/list.xml';
        $curl = new CurlRequest($export_tpls_path);

        $curl->get();
        $xml_content=$curl->result();
    	$data = array();

    	$xml = XML_unserialize($xml_content);
    	foreach($xml['templates']['template'] as $key=>$value){
    		$data[] =  array('id' => $value['id'],
    		                 'name' => $value['name']);
    	}
    	return $data;
    }

    function getBackendthemes(){
        $hDir = _SRV_WEBROOT.'xtFramework/library/ext/resources/css/';
        $FilesArray = array();
        if ($handle = opendir($hDir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strpos($file,'xtheme') !== false) {
                    $FilesArray[] =  array('id'=>$file,'name'=>$file);
                }
            }
            closedir($handle);
        }
        return $FilesArray;
    }
}

?>