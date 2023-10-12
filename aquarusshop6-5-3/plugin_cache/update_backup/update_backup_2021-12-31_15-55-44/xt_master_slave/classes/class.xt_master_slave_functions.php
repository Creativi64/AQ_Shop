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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/config.php';
require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_products.php';

class xt_master_slave_functions extends xt_backend_cls
{
    protected static $masterPriceViewFlags = array('mp' => 'MASTER_PRICE', 'ap' => 'FROM_PRICE', 'rp' => 'RANGE_PRICE', 'np' => 'NO_PRICE');

    public static function get_master_price_view_flags ()
    {
        $ret = array();
        $i = 0;
        foreach (self::$masterPriceViewFlags as $pvfKey => $pvfVal) {
            $ret[$i]['id'] = $pvfKey;
            $ret[$i]['name'] = $pvfVal;
            $i++;
        }
        return $ret;
    }
	
	protected static function get_Current_product_id ()
	{
		  global $current_product_id;
		  return  $current_product_id;
	}
	
    static function get_slave_from_master ($productsModel_)
    {
        global $db;
        static $get_slave_from_master_cache = array();

        if(!array_key_exists($productsModel_, $get_slave_from_master_cache))
        {
            $sql_where = " AND p.products_master_model=? ";

            $sdata_sql_products = new getProductSQL_query();
            $sdata_sql_products->setPosition('plugin_ms_sdata_sql_products_sdata');
            $sql_tablecols = 'p.*';
            $sdata_sql_products->setSQL_COLS(", " . $sql_tablecols);
            $sdata_sql_products->setSQL_WHERE($sql_where);

            $sql = $sdata_sql_products->getSQL_query();
            /** @var ADORecordSet $record */
            $record = $db->CacheExecute($sql,array( $productsModel_));
            while (!$record->EOF)
            {
                $get_slave_from_master_cache[$productsModel_][] = $record->fields;
                $record->MoveNext();
            }
            $record->Close();
        }

        return $get_slave_from_master_cache[$productsModel_];
    }


    /**
     * builds group prices forn amster product.
     * returns only max and min prices
     *
     * @param $productsModel_
     * @param $masterPriceContainer_
     * @param $masterProduct
     * @return mixed
     */
    public static function getPrices($productsModel_, $masterProduct)
    {
        global $msp;

        static $getPrices_cache = array();
        if(array_key_exists($productsModel_, $getPrices_cache))
        {
            return $getPrices_cache[$productsModel_];
        }

        if ($msp->pID != $masterProduct->data['products_id']) {
            $tmp_msp = new master_slave_products();
            $tmp_msp->setProductID($masterProduct->data['products_id']);
            $tmp_msp->getMasterSlave();
            if(count($tmp_msp->possibleProducts) > 0)
            {
                $slaves = $tmp_msp->possibleProducts;
            }
            else{
                $slaves = $tmp_msp->possibleProducts_primary;
            }
        }
        else {
            if(count($msp->possibleProducts) > 0)
            {
                $slaves = $msp->possibleProducts;
            }
            else{
                $slaves = $msp->possibleProducts_primary;
            }
        }

        if(count($slaves)==0) return false;

        $from = PHP_INT_MAX;
        $to =   -1;

        foreach ($slaves as $slavesR)
        {
            $p = new product(0);
            if(!is_array($slavesR) /*count($msp->possibleProducts) == 0*/)
            {
                $p->pID = $slavesR;
                $slavesR = $p->getProductData('price', false);
            }

            $p->pID = $slavesR['products_id'];
            $p->data = $slavesR;
            $p->data['products_price'] = $p->_getPrice(array('format'=>false, 'curr'=>true, 'qty'=>1));

            if(isset($p->data['products_price']['old_plain_otax']))
            {
                if ($p->data['products_price']['old_plain_otax'] < $from)
                {
                    $from = $p->data['products_price']['plain_otax'];
                }
                if ($p->data['products_price']['old_plain_otax'] > $to)
                {
                    $to = $p->data['products_price']['plain_otax'];
                }
            }
            else if(isset($p->data['group_price']) && is_array($p->data['group_price']))
            {
                if ($p->data['group_price']['prices'][count($p->data['group_price']['prices'])-1]['price'] < $from)
                {
                    $from = $p->data['group_price']['prices'][count($p->data['group_price']['prices'])-1]['price'];
                }
                if ($p->data['group_price']['prices'][0]['price'] > $to)
                {
                    $to = $p->data['group_price']['prices'][0]['price'];
                }
            }
            else
            {
                if ($p->data['products_price']['plain_otax'] < $from)
                {
                    $from = $p->data['products_price']['plain_otax'];
                }
                if ($p->data['products_price']['plain_otax'] > $to)
                {
                    $to = $p->data['products_price']['plain_otax'];
                }
            }
        }

        $data = array('from' => $from, 'to' => $to);

        $getPrices_cache[$productsModel_] = $data;
        return $data;
    }


    public static function getPricesORIG ($productsModel_, $masterProduct)
    {
        global $price, $tax;

        static $getPrices_cache = array();
        if(array_key_exists($productsModel_, $getPrices_cache))
        {
            return $getPrices_cache[$productsModel_];
        }

        $xt_ms_p = new master_slave_products();
        $xt_ms_p->setProductID($masterProduct->pID);
        if ($_POST['action']!='select_ms') {
            $xt_ms_p->unsetFilter();
        }
        else {
            $xt_ms_p->setFilter($_POST['ms_attribute_id']);
        }
        $xt_ms_p->getPossibleData($productsModel_);

        $from = PHP_INT_MAX;
        $to =   -1;

        $slaves = self::get_slave_from_master($productsModel_);
        if (count($slaves) > 0) {
            foreach ($slaves as $slavesR)
            {
                if($xt_ms_p->possibleProducts && count($xt_ms_p->possibleProducts) && !in_array($slavesR['products_id'], $xt_ms_p->possibleProducts)) {
                    continue;
                }
                //$p = product::getProduct($slavesR->fields['products_id'],'price',1, false);

                $p = new product(0);
                $p->pID = $slavesR['products_id'];
                $p->data = $slavesR;
                $p->data['products_price'] = $p->_getPrice(array('format'=>false, 'curr'=>true, 'qty'=>1));

                if($p->data['products_price']['old_plain_otax'])
                {
                    if ($p->data['products_price']['old_plain_otax'] < $from)
                    {
                        $from = $p->data['products_price']['plain_otax'];
                    }
                    if ($p->data['products_price']['old_plain_otax'] > $to)
                    {
                        $to = $p->data['products_price']['plain_otax'];
                    }
                }
                else
                {
                    if ($p->data['products_price']['plain_otax'] < $from)
                    {
                        $from = $p->data['products_price']['plain_otax'];
                    }
                    if ($p->data['products_price']['plain_otax'] > $to)
                    {
                        $to = $p->data['products_price']['plain_otax'];
                    }
                }
            }
        }

        $data = array('from' => $from, 'to' => $to);

        $getPrices_cache[$productsModel_] = $data;
        return $data;
    }

    /**
     * builds group prices from master product.
     * returns only max and min prices
     *
     * @param $productsModel_
     * @param $masterPriceContainer_
     * @param $masterProduct
     * @return mixed
     */
    public static function getGroupPrices ($productsModel_, $masterProduct)
    {
        global $price, $tax;

        static $getGroupPrices_cache = array();
        if(array_key_exists($productsModel_, $getGroupPrices_cache))
        {
            return $getGroupPrices_cache[$productsModel_];
        }

        $xt_ms_p = new master_slave_products();
        $xt_ms_p->setProductID($masterProduct->pID);
        if ($_POST['action']!='select_ms') {
            $xt_ms_p->unsetFilter();
        }
        else {
           $xt_ms_p->setFilter($_POST['ms_attribute_id']);
        }
        $xt_ms_p->getPossibleData($productsModel_, false);

        $from = array('cheapest' => PHP_INT_MAX);
        $to =   array('prices' => array('price' => -1));


        if(count($xt_ms_p->possibleProducts) == 0) return false;

        foreach ($xt_ms_p->possibleProducts as $slavesR)
        {
            $p = new product(0);
            if(!is_array($slavesR) /*count($msp->possibleProducts) == 0*/)
            {
                $p->pID = $slavesR;
                $slavesR = $p->getProductData('price', false);
            }
            $p->pID = $slavesR['products_id'];
            $p->data = $slavesR;
            $p->data['products_price'] = $p->_getPrice(array('format'=>false, 'curr'=>true, 'qty'=>1));

            if($p->data['group_price']['cheapest'] && $p->data['group_price']['cheapest'] < $from['cheapest'])
            {
                $from = $p->data['group_price'];
            }
            if($p->data['group_price']['prices'][0]['price'] > $to['prices'][0]['price'])
            {
                $to = $p->data['group_price'];
            }

        }

        if ($from['cheapest'] == PHP_INT_MAX  || $to['prices'][0]['price'] == -1)
        {
            return false;
        }

        $prices[] = $to['prices'][0];
        $prices[] = $from['prices'][count($from['prices'])-1];

        $prices = $from['prices'];

        $data = array('price' => $to['price']);

        $data['no_graduated']=0;
        //only one check rule
        if(sizeof($prices) == 1 && $prices[0]['qty']<2){
            for ($i = 0, $n = sizeof($prices); $i < $n; $i++) {
                $prices[$i]['price'] = $price->_calcCurrency($prices[$i]['price']);
            }
            $data['no_graduated']=1;
        } else{
            for ($i = 0, $n = sizeof($prices); $i < $n; $i++) {
                //$prices[$i]['price'] = $price->_calcCurrency($prices[$i]['price']);
            }
            $data['prices']=$prices;
            $size = count($prices);
            if ($size==0) $data['no_graduated']=1;

            //$last = array_pop($prices);
            $data['cheapest'] = $prices[$size-1]['price'];
        }

        $getGroupPrices_cache[$productsModel_] = $data;

        return $data;
    }


 /*
   * Slave Products_id
   * get the slave products_id 
   * if more than one slave found return master products_id 
   * */
    public static function slave_products_id ($productsModel_,$productsID)
    {
        /** @var ADORecordSet $slavesR */
		$slavesR = self::get_slave_session($productsModel_,$productsID);
		$res='';

        if (count($slavesR) == 1)
		{
			$res = product::getProduct($slavesR->fields['products_id'], 'full', '', '', 'product_info');

        }

        $slavesR->Close();
        return $res;
    }

	public static function returnSlavesAttributes ($productsModel_)
    { 	global $db,$language;
       	
    	static $_slavesAttrCache = array();
    	
    	if (isset($_slavesAttrCache[$productsModel_])) {
    		return $_slavesAttrCache[$productsModel_];
    	}
		//$slavesR = self::get_slave_session($productsModel_,$productsID);
		$slaves = self::get_slave_from_master($productsModel_);
		$res=array();

        if (count($slaves) > 0) {
            $pids = array_column($slaves,'products_id');
            $pids = implode(',',$pids);


            $sql = " SELECT pta.*,pa.*, pd.*,pt.* FROM   " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pta INNER JOIN
                        ".TABLE_PRODUCTS_ATTRIBUTES." pa ON pa.attributes_id = pta.attributes_id LEFT JOIN
                        ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." pd ON pd.attributes_id = pa.attributes_id LEFT JOIN
                        ".TABLE_PRODUCTS_ATTRIBUTES_TEMPLATES." pt ON pt.attributes_templates_id = pa.attributes_templates_id
                        WHERE pta.products_id in (?) and pd.language_code=?";

            /** @var ADORecordSet $record */
            $record = $db->CacheExecute($sql,array($pids,$language->code));
            while (!$record->EOF){
                array_push($res,$record->fields);
                $record->MoveNext();
            }
            $record->Close();
        }
        $_slavesAttrCache[$productsModel_] = $res;
        return $res;
    }
	
	public static function returnSingleSlaveAttributes ($id)
    {
        global $db,$language;

        static $_singleAttributesCache = array();

        if (isset($_singleAttributesCache[$id])) {
            return $_singleAttributesCache[$id];
        }

		$res=array();
		$sql = " SELECT pta.* FROM   " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pta 
      				WHERE pta.products_id=? ";

        /** @var ADORecordSet $record */
   		$record = $db->CacheExecute($sql,array((int)$id));
        if ($record->RecordCount() > 0) {
            while (!$record->EOF) {
                array_push($res,array($record->fields['attributes_parent_id']=>$record->fields['attributes_id']));
                $record->MoveNext();
            }
            $record->Close();
        }

        $_singleAttributesCache[$id] = $res;

        return $res;
    }

    public static function returnAttributesCount ($id)
    {
        global $db;

        static $_attributesCountCache = array();

        if (isset($_attributesCountCache[$id])) {
            return $_attributesCountCache[$id];
        }

        $res=array();
        $sql = " SELECT pta.* FROM   " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pta
      				WHERE pta.products_id=? ";

        /** @var ADORecordSet $record */
        $record = $db->CacheExecute($sql,array((int)$id));
        if ($record->RecordCount() > 0) {
            while (!$record->EOF) {
                array_push($res,array($record->fields['attributes_parent_id']=>$record->fields['attributes_id']));
                $record->MoveNext();
            }
            $record->Close();
        }

        $_attributesCountCache[$id] = $res;

        return $res;
    }

    public static function returnSelectedSlaveAttributes ($id)
    { 	global $db,$language;

        static $_selectedAttributesCache = array();

        if (isset($_selectedAttributesCache[$id])) {
            return $_selectedAttributesCache[$id];
        }

        $res=array();
        $sql = " SELECT ptad.attributes_name AS option_name, ptad_parent.attributes_name AS group_name FROM   " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pta
                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION ." ptad ON (pta.attributes_id=ptad.attributes_id AND ptad.language_code='" . $language->code . "')
                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION. " ptad_parent ON (pta.attributes_parent_id=ptad_parent.attributes_id AND ptad_parent.language_code='" . $language->code . "')
      			WHERE pta.products_id=? ";

        /** @var ADORecordSet $record */
        $record = $db->CacheExecute($sql,array((int)$id));
        if ($record->RecordCount() > 0) {
            while (!$record->EOF) {
                array_push($res,$record->fields);
                $record->MoveNext();
            }
            $record->Close();
        }

        $_selectedAttributesCache[$id] = $res;

        return $res;
    }
	
	/*
		Returns slaves based on selected master options (based on session)
	*/
	public static function get_slave_session($products_model,$productsID)
	{
        global $db;
		$tt = self::get_slave_from_master($products_model);

		if (count($tt))
		{
			$add_more = '';
			foreach ($tt as $tt_entry) {
					$add_more  .= (($add_more=='')?'':', '). (int)$tt_entry['products_id'];
			}
			$add_more = ' and p.products_id in ('.$add_more.')';
		}

		$add_to_where ='';
		$add_to_table='';
		$i=1;

		if (isset($_SESSION['select_ms'][$productsID]["id"]))
		{
		foreach ($_SESSION['select_ms'][$productsID]["id"] as $key => $val) {
				$add_to_where .=  (($add_to_where=='')?' ': " and ")." pa".$i.".attributes_id = ". (int)$val;
				$add_to_table .= " LEFT JOIN ". TABLE_PRODUCTS_TO_ATTRIBUTES." pa".$i." ON pa".$i.".products_id = p.products_id ";
				$i++;
			}
			if ($add_to_where!='') $add_to_where =' and('.$add_to_where.')';
		}


		$sql_where = "";
        $sql_where .= " WHERE p.products_status = '1'";
        if (_STORE_STOCK_CHECK_DISPLAY == 'false' && _SYSTEM_STOCK_HANDLING == 'true') {
            $sql_where .= " AND p.products_quantity > 0";
        }

        $sql = "
          SELECT DISTINCT p.products_id, p.products_price, p.products_image, p.products_master_model, p.products_model
          FROM   " . TABLE_PRODUCTS . " p  ".$add_to_table." ". $sql_where .$add_to_where.$add_more. ";";

        $record = $db->CacheExecute($sql);
        return $record;
	}
	
	/* returns master data by products_model*/
    public static function getMasterData($productsModel)
    {
        global $db, $language, $store_handler;
        static $getMasterData_cache = array();

        if(!array_key_exists($productsModel, $getMasterData_cache))
        {
            $sql = "SELECT p.products_id, p.products_image,p.products_model,p.products_id,p.products_option_master_price,
p.ms_load_masters_free_downloads,
p.ms_load_masters_main_img,
p.load_mains_imgs,
p.sum_quantity_for_graduated_price,   
p.products_keywords_from_master,
p.products_short_description_from_master,
p.products_description_from_master,
p.ms_filter_slave_list,
p.ms_filter_slave_list_hide_on_product,
p.ms_show_slave_list,
p.ms_open_first_slave,
pd.products_description,
pd.products_short_description,
pd.products_keywords

FROM   " . TABLE_PRODUCTS . " p
            LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id
            where     p.products_model=? and pd.language_code=? and pd.products_store_id=?";

            /** @var ADORecordSet $record */
            $record = $db->CacheExecute($sql,array($productsModel, $language->content_language, $store_handler->shop_id));
            $getMasterData_cache[$productsModel] = $record->fields;
        }
        return $getMasterData_cache[$productsModel];
    }

	
	/* returns image by ID*/
	public static function productImage($productsID)
	{
		global $db;
		$sql = "SELECT products_image FROM   " . TABLE_PRODUCTS . " where 	products_id=?";

        /** @var ADORecordSet $record */
        $record = $db->CacheExecute($sql,array((int)$productsID));
		if ($record->fields["products_image"]!='')
			return 'product:'.$record->fields["products_image"];
		else return 'product:'._STORE_PRODUCT_NO_PICTURE;
	}
	 /*
   * Slave Image
   * get the slave image 
   * if no slave image found return master image 
   * */
    public static function slave_image($productsModel_, $master_image,$productsID,$load_master='1',$current_item_image='')
    {
		if (strpos($master_image, 'product:')===false){
            $master_image = 'product:'.$master_image;
        }
		if (/*_PLUGIN_MASTER_SLAVE_STAY_ON_MASTER_URL=='1' ||*/ true) { // mode is either load slave in master and ajax
         
            if ($current_item_image==''){
                /** @var ADORecordSet $slavesR */
                $slavesR = self::get_slave_session($productsModel_,$productsID);
                if ($slavesR->RecordCount() ==1) 
                {	
                	while (!$slavesR->EOF) {
                	   if ($slavesR->fields['products_image']!='') $master_image2 = 'product:'.$slavesR->fields['products_image'];
                	   else $master_image2 ='product:'._STORE_PRODUCT_NO_PICTURE;
                	   $slavesR->MoveNext();
                	}
                	$slavesR->Close();
                }else $master_image2 = $master_image; // set master iamge
            }else {
                $master_image2 = $current_item_image; // set currenct image
            }
            
        }else { // mode is redirect to slave
             if ($current_item_image=='') // still in master product 
                $master_image2 = $master_image; 
             else $master_image2 = $current_item_image; 
        }

		if (empty($load_master))
			return $master_image2;
		else return $master_image;
    }
	
	/**
	 * 
	 * unset filter in SESSION
	 */
	public static function unsetFilter() {
		
		if (($_SESSION['select_ms']['action'] != 1 and $_GET['action_ms'] != 1) or $_GET['reset_ms'] == 1) {
			unset($_SESSION['select_ms']/*[$this->pID]*/);
		}
	}

	
	/**
	 * 
	 * set filter in SESSION
	 * 
	 * @param array $data option and its value
	 */
	public static function setFilter($data,$pid) {
		
		foreach ($data as $key => $val) {
			if ($val != 0) {
				$_SESSION['select_ms'][$pid]['id'][$key] = $val;
			} else {
				unset($_SESSION['select_ms'][$pid]['id'][$key]);
				//$this->unset = true;
			}
		}
	}
	
	/*
     * 
     * returns array of not selected options for a master 
     * 
     * @param string $products_model  - master model products' model
     */
    public static function getNotSelectedOptions($products_model) {
        $attributes = array();
        $res  = xt_master_slave_functions::returnSlavesAttributes($products_model);
        foreach($res as $att){
            if (!in_array($att["attributes_parent_id"], $attributes)){
                array_push($attributes,$att["attributes_parent_id"]);
            }
        } 
        $selected = array();
        foreach($_SESSION['select_ms'] as $selected){
            foreach($selected['id'] as $k=>$val){
                if (!in_array($k, $selected)){
                    array_push($selected,$k);
                }
            }
        }
        $not_selected = array();
        foreach($attributes as $a){
            if (!in_array($a, $selected)){
                array_push($not_selected,$a);
            }
        }
        return  $not_selected;
    }

    /**
     * @param $plgValue int  the value in plugin config
     * @param $productsKey string  the key in products table
     * @param null $main_product the product to observe; pass an real product or array from getMasterData()
     * @return bool
     */
    public static function getOverrideSetting($plgValue, $productsKey, $main_product = null, $variant = null)
    {
        $ret = false;

        if(!is_object($main_product) && !is_array($main_product))
        {
            $products_id = (int) $main_product;
            if ($products_id)
            {
                $main_product = product::getProduct($products_id);
            }
        }

        if(is_array($main_product))
        {
            $product_obj = new stdClass();
            $product_obj->data = $main_product;
            $main_product = $product_obj;
        }
        else if (empty($main_product) || !$main_product->is_product)
        {
            return false;
        }

        $ret = false;
        if($variant)
        {
            if($variant->data[$productsKey] == 0) {
                $ret = false;
            }
            else if($variant->data[$productsKey] == 1) {
                $ret = true;
            }
            else if($variant->data[$productsKey] == 2 && $main_product->data[$productsKey] == 2 && $plgValue == 1) {
                $ret = true;
            }
            else if($variant->data[$productsKey] == 2 && $main_product->data[$productsKey] == 2 && $plgValue == 0) {
                $ret = false;
            }
            else if($variant->data[$productsKey] == 2 && $main_product->data[$productsKey] == 1) {
                $ret = true;
            }
            else if($variant->data[$productsKey] == 2 && $main_product->data[$productsKey] == 0) {
                $ret = false;
            }
        }
        else {
            if($main_product->data[$productsKey] == 0) {
                $ret = false;
            }
            else if($main_product->data[$productsKey] == 1) {
                $ret = true;
            }
            else if($main_product->data[$productsKey] == 2 && $plgValue == 1) {
                $ret = true;
            }
            else if($main_product->data[$productsKey] == 2 && $plgValue == 0) {
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * @param $plgValue int  the value in plugin config
     * @param $productsKey string  the key in products table
     * @param null $main_product the product to observe; pass an real product or array from getMasterData()
     * @return int
     */
    public static function getImagesOverrideSetting($plgValue, $productsKey, $main_product = null, $variant = null)
    {
        $ret = false;

        if(!is_object($main_product) && !is_array($main_product))
        {
            $products_id = (int) $main_product;
            if ($products_id)
            {
                $main_product = product::getProduct($products_id);
            }
        }

        if(is_array($main_product))
        {
            $product_obj = new stdClass();
            $product_obj->data = $main_product;
            $main_product = $product_obj;
        }
        else if (empty($main_product) || !$main_product->is_product)
        {
            return 0;
        }

        $ret = 0;
        $mains_key_val = $main_product->data[$productsKey];
        $variants_key_val = $variant->data[$productsKey];
        if($variant)
        {
            if($variants_key_val == 1 || $variants_key_val == 3) {
                $ret = $variants_key_val;
            }
            else if($variants_key_val == 2 && $mains_key_val == 2) {
                $ret = $plgValue;
            }
            else if($variants_key_val == 2 && $mains_key_val) {
                $ret = $mains_key_val;
            }
        }

        return $ret;
    }

    public function apply_variantProduct_price($data)
    {
        global $db;

        $ret = new stdClass();
        $ret->success = false;

        /** @var Countable|bool $products_data */
        $products_data = $db->GetArray('SELECT products_model, products_price FROM '.TABLE_PRODUCTS. ' WHERE products_id=? AND products_master_flag=1', [$data['products_id']]);
        if(count($products_data) == 1)
        {
            $db->Execute('UPDATE '.TABLE_PRODUCTS. ' SET products_price=? WHERE products_master_model=? AND products_master_flag=0', [$products_data[0]['products_price'], $products_data[0]['products_model']]);
            $ret->success = true;
        }

        return $ret;
    }

}
