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

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_master_slave/classes/class.xt_master_slave_functions.php';

class master_slave_products
{

	var $pID;
	var $possibleProducts;
	var $possibleOptions;
	var $possibleValues;
	var $fullData;
	var $productOptions;
	///
	var $unset;
	var $delete_image;
	var $master_model;
	var $showProductList;
	var $showProductList_filtered;
	var $allOptions;
	var $allValues;

	var $possibleProducts_primary;
	var $possibleOptions_primary;
	var $possibleValues_primary;
	var $not_master_slave_pr = false;
	var $slave_no_options=false;

	private $_products_model = null;


	public $flag_processingProductList = false;
	public $allProduct_ids = array();

	/**
	 *
	 * constructor
	 */
	public function __construct()
	{
		$this->delete_image = 'small_delete.gif';
		$this->unset = false;
	}


	/**
	 *
	 * unknown
	 *
	 * @param int $pID
	 * @return void
	 */
	function getProductLink($pID)
	{
		$link = xtc_product_link($pID, xtc_get_products_name($pID));
	}


	/**
	 *
	 * set product ID - maybe redirect to master product
	 *
	 * @param int $pID ID of product (master or slave)
	 */
	function setProductID($pID, $products_model = null)
	{
		$this->pID = $pID;
		$this->_products_model = $products_model;
	}


	/**
	 *
	 * redirect to given product with ID
	 * trying to pass filter as GET; not working, yet
	 *
	 * @param int $id product ID
	 */
	function _redirect($id)
	{
		global $xtLink,$xtPlugin;
		// copy options from current to next product
		$_SESSION['select_ms'][$id]['id'] = $_SESSION['select_ms'][$this->pID]['id'];

		$p_info = product::getProduct($id,'default', '', '', 'product_info');

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:_redirect')) ? eval($plugin_code) : false;
		if ((_PLUGIN_MASTER_SLAVE_STAY_ON_MASTER_URL=='1') )
		{
			$link_array = array('page' => 'product', 'type' => 'product', 'name' => $p_info->data['products_name'], 'id' => $p_info->data['products_id'], 'seo_url' => $p_info->data['url_text']);
		}
		else
		{
			$link_array = array('page'=> 'product', 'type'=>'product', 'name'=>$p_info->data['products_name'], 'id'=>$p_info->data['products_id'],'seo_url'=>$p_info->data['url_text']);
		} //, 'params'=>'action_ms=1');

		$xtLink->_redirect($xtLink->_link($link_array));
	}


	/**
	 *
	 * unset filter in SESSION
	 */
	function unsetFilter($add_to_session_name = '')
	{
		if (($_SESSION['select_ms']['action'] != 1 and $_GET['action_ms'] != 1) /*or $_GET['reset_ms'] == 1*/)
		{
			unset($_SESSION['select_ms'.$add_to_session_name]/*[$this->pID]*/);
		}
	}


	/**
	 *
	 * set filter in SESSION
	 *
	 * @param array $data option and its value
	 */
	function setFilter($data, $add_to_session_name = '')
	{

		if (empty($data)) {
			$this->unsetFilter();
			return;
		}

		foreach ($data as $key => $val)
		{
			if ($val != 0)
			{
				$_SESSION['select_ms'.$add_to_session_name][$this->pID]['id'][$key] = $val;
			}
			else
			{
				unset($_SESSION['select_ms'.$add_to_session_name][$this->pID]['id'][$key]);
				$this->unset = true;
			}
		}
	}


	/**
	 *
	 * get filter (for given product ID) from SESSION
	 *
	 * @return array
	 */
	function getFilter($add_to_session_name = '')
	{
		if (!is_array($_SESSION['select_ms' . $add_to_session_name][$this->pID]['id']))
		{
			return array();
		}
		reset($_SESSION['select_ms'.$add_to_session_name][$this->pID]['id']);
		return $_SESSION['select_ms'.$add_to_session_name][$this->pID]['id'];
	}


	/**
	 *
	 * check if filter is set (for product ID)
	 *
	 * @return bool
	 */
	function isFilter($add_to_session_name = '')
	{
		if (isset($_SESSION['select_ms'.$add_to_session_name][$this->pID]['id']) && count($_SESSION['select_ms'.$add_to_session_name][$this->pID]['id']) > 0)
		{
			return true;
		}
	}


	/**
	 *
	 * aka main; is called by plugin
	 *
	 * @return bool|void
	 */
	function getMasterSlave()
	{
		global $xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getMasterSlave_top')) ? eval($plugin_code) : false;
		if ($this->pID != '')
		{
			$modelTmp = $this->getModel($this->pID);
			/*
			// get (master) model number; might be false
			if (empty($this->_products_model))
			{
				$model = $modelTmp;
			}
			else{

			}
			*/
            $model = $modelTmp;

			if ($model)
			{
				$this->getPossibleData($model);
				//$this->productOptions = $this->getOptions();
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}


	/**
	 *
	 * merge two option arrays into one
	 *
	 * @param array $possibleOptions_arr
	 * @param array $allOptions_arr
	 * @return array|bool $mergedOptions_arr array with merged options or false
	 */
	public function mergeOptions($possibleOptions_arr, $allOptions_arr, $optionSet_arr_primary = '')
	{
		///
		global $xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:mergeOptions_top')) ? eval($plugin_code) : false;
		if (!is_array($possibleOptions_arr) or !is_array($allOptions_arr))
		{
			return false;
		}
		//var_dump($optionSet_arr_primary);
		$mergedOptions_arr = $allOptions_arr;

		foreach ($mergedOptions_arr as $key => $oneDropdown)
		{
			$data_arr = $oneDropdown['data'];
			foreach ($data_arr as $count_num => $dropdownField_arr)
			{
				$fieldStatus_str = $this->checkStatus($dropdownField_arr['id'], $possibleOptions_arr, $optionSet_arr_primary);
				if ($fieldStatus_str == 'missing')
				{
					$mergedOptions_arr[$key]['data'][$count_num]['disabled'] = true;
				}
				elseif ($fieldStatus_str == 'selected')
				{
					$mergedOptions_arr[$key]['data'][$count_num]['selected'] = true;
					$mergedOptions_arr[$key]['selected'] = $dropdownField_arr['text'];
				}
			}
		}
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:mergeOptions_bottom')) ? eval($plugin_code) : false;
		return $mergedOptions_arr;
	}


	/**
	 *
	 * check status of option - selected, missing or normal
	 *
	 * @param int $id_num ID of option
	 * @param array $options_arr
	 * @return string
	 */
	public function checkStatus($id_num, $options_arr, $option_arr_primary = '')
	{
		///
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:checkStatus')) ? eval($plugin_code) : false;
		foreach ($options_arr as $key => $oneDropdown)
		{
			$data_arr = $oneDropdown['data'];

			foreach ($data_arr as $count_num => $dropdownField_arr)
			{
				if ($dropdownField_arr['id'] == $id_num)
				{
					if (isset($dropdownField_arr['selected']))
					{
						return 'selected';
					}
					else
					{
						return 'normal';
					}
				}
			}
		}

		//var_dump($option_arr_primary);
		if (count($option_arr_primary)>0 && is_array($option_arr_primary))
		{
			foreach ($option_arr_primary as $oneDropdown2)
			{
				$data_arr = $oneDropdown2['data'];

				foreach ($data_arr as $count_num => $dropdownField_arr2)
				{
					if ($dropdownField_arr2['id'] == $id_num)
					{
						if (isset($dropdownField_arr2['selected']))
						{
							return 'selected';
						}
						else
						{

							return 'normal';
						}
					}
				}
			}
		}
		return count($this->allOptions)> 1 ? 'missing' : 'normal';
	}


	/**
	 *
	 * write all options and values into class attributes
	 *
	 * @param array $possibleData_arr
	 * @return bool
	 */
	public function setAllOptions($possibleData_arr = array())
	{
		global $xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:setAllOptions')) ? eval($plugin_code) : false;
		if (empty($possibleData_arr))
		{
			return false;
		}
		else
		{
			$this->allOptions = array();
			$this->allValues = array();
			foreach ($possibleData_arr as $key => $val_arr)
			{
				$this->allOptions[] = $val_arr['attributes_parent_id'];
				$this->allValues[]  = $val_arr['attributes_id'];
			}

			// remove duplicates
			$this->allOptions = array_unique($this->allOptions);
			$this->allValues = array_unique($this->allValues);

			return true;
		}
	}


	/**
	 *
	 * returns master model number if slave, else model number
	 * or false if pID not found
	 *
	 * @param int $pID product ID
	 * @return string|bool
	 */
	function getModel($pID)
	{
		global $product, $db,$xtPlugin;

		$pID = (int)$pID;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getModel')) ? eval($plugin_code) : false;
		$sql_tablecols = 'p.products_model, p.products_master_model';
		$model_sql_products = new getProductSQL_query();
		$model_sql_products->setPosition('plugin_ms_getModel');
		$model_sql_products->setSQL_COLS(", " . $sql_tablecols);
		$model_sql_products->setSQL_WHERE(" AND p.products_id = ?");

		$query = "".$model_sql_products->getSQL_query()."";

		$record = $db->CacheExecute($query,array((int)$pID));
		if ($record->RecordCount() > 0)
		{

			if (!$record->fields['products_master_model'])
			{
				$this->master_model = $record->fields['products_model'];
				return $record->fields['products_model'];
			}
			else
			{
				$this->master_model = $record->fields['products_master_model'];
				return $record->fields['products_master_model'];
			}
		}
		else
		{
			return false;
		}
	}


	/**
	 *
	 * verify whether current product is a slave
	 *
	 * @return bool|int|void false or product ID of master product or NOTHING
	 */
	function isSlave()
	{
		global $product, $db,$xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:isSlave')) ? eval($plugin_code) : false;
		$this->isSlave = true;

		// check whether product has a master model number
		$sql_tablecols = 'p.products_master_model';
		$model_sql_products = new getProductSQL_query();
		$model_sql_products->setPosition('plugin_ms_isSlave_model');
		$model_sql_products->setSQL_COLS(", " . $sql_tablecols);
		$model_sql_products->setSQL_WHERE(" AND p.products_master_model != '' and p.products_id = ?");

		$model_query = "".$model_sql_products->getSQL_query()."";
		$record = $db->CacheExecute($model_query,array((int)$this->pID));

		// get pID of product with detected master model number
		if ($record->RecordCount() > 0)
		{

			$master_sql_tablecols = 'p.products_master_model';
			$master_model_sql_products = new getProductSQL_query();
			$master_model_sql_products->setPosition('plugin_ms_isSlave_master_model');
			$master_model_sql_products->setSQL_COLS(", " . $master_sql_tablecols);
			$master_model_sql_products->setSQL_WHERE(" AND p.products_master_model = '' and p.products_model = ?");

			$query = "".$master_model_sql_products->getSQL_query()."";
			$master_record = $db->CacheExecute($query,array($record->fields['products_master_model']));

			// return pID if found
			if ($master_record->RecordCount() > 0)
			{
				return $master_record->fields['products_id'];
			}
			// ELSE ???
		}
		else
		{
			$this->isSlave = false;
			return false;
		}
	}

	function getMaster($id = 0)
	{
		global $product, $db,$xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getMaster')) ? eval($plugin_code) : false;
		// check whether product has a master model number
		$sql_tablecols = 'p.products_master_model, su.url_text';
		$model_sql_products = new getProductSQL_query();
		$model_sql_products->setPosition('plugin_ms_isSlave_model');
		$model_sql_products->setSQL_COLS(", " . $sql_tablecols);
		if ($id > 0)
		{
			$model_sql_products->setSQL_WHERE(" and p.products_id = ? and p.products_master_flag = 1");
			$sec_key = array((int)$id);
		}
		else
		{
			$model_sql_products->setSQL_WHERE(" and p.products_id = ? and p.products_master_flag = 1");
			$sec_key = array((int)$this->pID);
		}
		$model_query = "".$model_sql_products->getSQL_query()."";

		$record = $db->CacheExecute($model_query,$sec_key);

		// get pID of product with detected master model number
		if ($record->RecordCount() > 0)
		{
			return $record->fields;
		}
		else
		{

			return false;
		}
	}

	public function getOptionsOrder($masterModel)
	{
		global $db, $language;

		// CEE-690-98461
        $firstSlaveModelFound = $db->GetOne('select products_model from '.TABLE_PRODUCTS.' where products_master_model =?', array($masterModel));
        $slavePID =$db->GetOne('select products_id from '.TABLE_PRODUCTS.' where products_model =? and products_master_model =? and products_master_flag != 1', array($firstSlaveModelFound, $masterModel));

        if(!$slavePID){
            $slavePID =$db->GetOne('select products_id from '.TABLE_PRODUCTS.' where products_master_model =?', array($firstSlaveModelFound));
        }
        // CEE-690-98461

		$option_data = $db->CacheExecute("select attributes_parent_id from ".TABLE_PRODUCTS_TO_ATTRIBUTES." where products_id = ?",array((int)$slavePID));
		$parent_attrs = array();

		if ($option_data->RecordCount() > 0)
		{
			while (!$option_data->EOF)
			{
				$parent_attrs[] = $option_data->fields['attributes_parent_id'];

				$option_data->MoveNext();
			}

			$odata = $db->CacheExecute("select distinct pa.*,pad.*, pat.* from " . TABLE_PRODUCTS_ATTRIBUTES . " pa

										left join ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." pad on pa.attributes_id = pad.attributes_id
										LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES_TEMPLATES." pat ON pat.attributes_templates_id = pa.attributes_templates_id
								   where pad.language_code = ? and pa.attributes_id in (".implode(",", $parent_attrs).") and
								   pa.status = 1 order by pa.sort_order, pad.attributes_name",
				array($language->code));

			$parent_attrs = array();

			if ($odata->RecordCount() > 0)
			{
				while (!$odata->EOF)
				{
					$parent_attrs[] = $odata->fields['attributes_id'];

					$odata->MoveNext();
				}
			}
		}

		$this->optionsOrder = $parent_attrs;
		return $parent_attrs;
	}

	/**
	 *
	 * writes possibleProducts, possibleOptions, possibleValues into class attributes
	 *
	 * @param string $model model number
	 */
	function getPossibleData($model='')
	{
		global $xtPlugin, $product, $db, $current_product_id,$xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getPossibleData_top')) ? eval($plugin_code) : false;
		// get options belonging to master model number
		$data_sql_tablecols = 'pa.products_id, pa.attributes_id, pa.attributes_parent_id';
		$data_sql_products = new getProductSQL_query();
		$data_sql_products->setPosition('plugin_ms_getPossibleData_data');
		$data_sql_products->setSQL_COLS(", " . $data_sql_tablecols);
		$data_sql_products->setSQL_TABLE("LEFT JOIN " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pa ON pa.products_id = p.products_id");
		$data_sql_products->setSQL_WHERE(" AND p.products_master_model = ? ");
		$data_sql_products->setSQL_SORT(' p.products_sort ASC');

		$data_query = "".$data_sql_products->getSQL_query()."";
		$data_record = $db->CacheExecute($data_query,array($model));

		// moved here from following if-branch
		$possibleData = array();
		if ($data_record->RecordCount() == 0)
		{
			$this->not_master_slave_pr = true;
		}

		if ($data_record->RecordCount() > 0 || !$this->isFilter())
		{
			// $possibleData = array();
			while (!$data_record->EOF)
			{
				if($data_record->fields['products_id'])
				{
					$possibleData[] = $data_record->fields;
				}
				$data_record->MoveNext();
			}
			$data_record->Close();
		}

		// superfluous ???
		// gets the master model number first
		// then gets the same infos as above
		else
		{
			$master_sql_tablecols = 'p.products_master_model';
			$master_sql_products = new getProductSQL_query();
			$master_sql_products->setPosition('plugin_ms_getPossibleData_master');
			$master_sql_products->setSQL_COLS(", " . $master_sql_tablecols);
			$master_sql_products->setSQL_WHERE(" AND p.products_model = ?");

			$master_query = "".$master_sql_products->getSQL_query()."";
			$master_record = $db->CacheExecute($master_query,array($model));

			if ($master_record->RecordCount() > 0)
			{
				$master_data_sql_tablecols = 'pa.products_id, pa.attributes_id, pa.attributes_parent_id';
				$master_data_sql_products = new getProductSQL_query();
				$master_data_sql_products->setPosition('plugin_ms_getPossibleData_master_data');
				$master_data_sql_products->setSQL_COLS(", " . $master_data_sql_tablecols);
				$master_data_sql_products->setSQL_TABLE("LEFT JOIN " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pa ON pa.products_id = p.products_id");
				$master_data_sql_products->setSQL_WHERE(" AND p.products_master_model = ?");

				$master_data_query = "".$master_data_sql_products->getSQL_query()."";

				$master_data_record = $db->CacheExecute($master_data_query,array($master_record->fields['products_master_model']));

				if ($master_data_record->RecordCount() > 0)
				{
					while (!$master_data_record->EOF)
					{
						if($master_data_record->fields['products_id'])
						{
							$possibleData[] = $master_data_record->fields;
						}

						$master_data_record->MoveNext();
					}
					$master_data_record->Close();
				}
			}
		}

		// save all options in additional class attribute
		$setAllOptions_bol = $this->setAllOptions($possibleData);

		/* ------------------------------------------------------------------ */

		$possibleData_count = count($possibleData);

		if ($possibleData_count > 0)
		{
			$filter_count = 0;
			$tmp_options = array();
			$allProducts = array();
			if ($this->isFilter())
			{
				$filter = $this->getFilter();
				$filter_count = count($filter);

				foreach($filter as $key => $value)
				{
					if ($value != 0)
					{
						$tmp_options[] = $key;
						$tmp_values[] = $value;
					}
				}

				$possibleDataGrouped = array();
				foreach($possibleData as $pd)
				{
					$possibleDataGrouped[$pd['products_id']][$pd['attributes_parent_id']] = $pd['attributes_id'];
				}

				if ($tmp_values)
				{
					foreach ($possibleDataGrouped as $product_id => $product_values)
					{
						$notfound = false;
						foreach($tmp_values as $value)
						{
							if (!in_array($value, $product_values))
							{
								$notfound = true;
								break;
							}
						}
						if($notfound) continue;
						$possibleProducts_left[] = $product_id;
					}
				}

				if ($tmp_values)
				{
					for ($i = 0; $i < $possibleData_count; $i++)
					{
						if (in_array($possibleData[$i]['attributes_id'], $tmp_values))
						{
							$possibleProducts[] = $possibleData[$i]['products_id'];
						}
						$allProducts[] = $possibleData[$i]['products_id'];
					}
				}
			}
			else
			{
				// default: use all products found in possibleData
				for ($i = 0; $i < $possibleData_count; $i++)
				{
					$possibleProducts[] = $possibleData[$i]['products_id'];
					$allProducts[] = $possibleData[$i]['products_id'];
				}
			}

			// partially superfluous
			$this->allProduct_ids = array_values(array_unique($allProducts)); // wird weiter unten übeschrieben ? ka was das eigentlich soll
			for ($i = 0; $i < $possibleData_count; $i++)
			{
				if (!in_array($possibleData[$i]['attributes_parent_id'], $tmp_options))
				{
					$tmp_options[] = $possibleData[$i]['attributes_parent_id'];
					$tmp_other_values[] = $possibleData[$i]['attributes_id'];
				}
			}

			$option_where = '';
			if ($tmp_options[0])
			{
				// does not seem to make a difference whether this is disabled
				$option_where.= " and attributes_parent_id IN (".implode(", ", $tmp_options).")";
			}
			else
			{
				$this->slave_no_options=true;
			}

			if (is_array($possibleProducts))
			{
				// array with counts of products
				$_pcount = array_count_values ($possibleProducts);
				$possibleProducts = array_unique($possibleProducts);

				if(isset($xtPlugin->active_modules['xt_customersdiscount']))
				{
					global $language, $customers_status;

					$table = $customers_status->permission->_table;
					$where = $customers_status->permission->_where;

					$record = $db->Execute(
						"SELECT cs.customers_status_id as id, csd.customers_status_name as text FROM " . TABLE_CUSTOMERS_STATUS_DESCRIPTION . " csd, ".TABLE_CUSTOMERS_STATUS." cs ".$table." where cs.customers_status_id = csd.customers_status_id and csd.language_code=? ".$where,
						array($language->code)
					);
					while(!$record->EOF){
						$data[] = $record->fields;
						$record->MoveNext();
					}$record->Close();

					$gnames = array('p.price_flag_graduated_all');
					foreach($data as $g)
					{
						$gnames[] = 'p.price_flag_graduated_'.$g['id'];
					}
				}
                $gnames[] = 'products_tax_class_id';
				$addCols = count($gnames) ? ', '.implode(', ',$gnames) : '';

                ($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getPossibleData_addCols')) ? eval($plugin_code) : false;

				//  formally p.* , pa.products_id, pa.attributes_id, pa.attributes_parent_id
				$qry = "select p.products_id, pa.attributes_id, pa.attributes_parent_id, p.products_price, flag_has_specials $addCols from " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pa
                left join ".TABLE_PRODUCTS." p on p.products_id = pa.products_id
                where pa.products_id in (".implode(",", $possibleProducts).")".$option_where."
                order by p.products_master_slave_order ";

				$record = $db->CacheExecute($qry);
				if ($record->RecordCount() > 0)
				{
					while (!$record->EOF)
					{
						$option_data[] = $record->fields;
						$record->MoveNext();
					}
					$record->Close();
				}

                //$this->allProduct_ids = array_column($option_data,'products_id');
                // fix  Artikel ändert sich nicht bei auwahl einer Eigenschaft/Attributs, wenn der Artikel nur 1 Attribut hat

				$possibleProducts = array();

				$count_data = count($option_data);

				for ($i = 0; $i < $count_data; $i++)
				{
					$possibleOptions[] = $option_data[$i]['attributes_parent_id'];

					if ($filter_count == $_pcount[$option_data[$i]['products_id']] || $filter_count == 0)
					{
						$possibleProducts[$option_data[$i]['products_id']] = $option_data[$i];
						$possibleValues[] = $option_data[$i]['attributes_id'];
					}
				}

				if (is_array($possibleProducts) && count($possibleProducts))
				{
					$this->possibleProducts = $possibleProducts;
				}

				if (is_array($possibleOptions))
				{
					$this->possibleOptions = array_unique($possibleOptions);
				}

				if (is_array($possibleValues))
				{
					$this->possibleValues = array_unique($possibleValues);
				}
				else {
					$this->possibleValues = $this->allValues;
				}
			}
			else
			{
				// not specified
			}
		}
		else
		{

			$sdata_sql_products = new getProductSQL_query();
			$sdata_sql_products->setPosition('plugin_ms_getPossibleData_sdata');
			$sdata_sql_products->setSQL_WHERE(" AND p.products_master_model = ?");
			$sdata_sql_products->setSQL_SORT(' p.products_sort ASC');

			$sdata_query = "".$sdata_sql_products->getSQL_query()."";
			$sdata_record = $db->CacheExecute($sdata_query,array($model));

			if ($sdata_record->RecordCount() > 0 || !$this->isFilter())
			{
				while (!$sdata_record->EOF)
				{
					if($sdata_record->fields['products_id'])
					{
						$possibleData[] = $sdata_record->fields;
					}
					$sdata_record->MoveNext();
				}
				$sdata_record->Close();
			}

			$possibleData_count = count($possibleData);

			for ($i = 0; $i < $possibleData_count; $i++)
			{
				$possibleProducts[] = $possibleData[$i]['products_id'];
			}

			if (is_array($possibleProducts))
			{
				$this->possibleProducts = array_unique($possibleProducts);
			}
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getPossibleData_bottom')) ? eval($plugin_code) : false;
		//Sort by products_sort
		//$this->possibleProducts  = $this->sortPossibleProducts($this->possibleProducts ); // TODO reenable sort ? oder ind die schon sortiert

		$this->SetPossible_primary($possibleData);
	}


	function SetPossible_primary($possibleData)
	{
		global $db,$xtPlugin;

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:SetPossible_primary_top')) ? eval($plugin_code) : false;
		$pos_count = count($possibleData);
		/* set possibeValues Based on primary filters*/
		if($pos_count > 0)
		{
			$fcount_primary = 0;
			$tmp_options_primary = array();

			if (($_POST['action'] == 'select_ms' or $_GET['action_ms'] == 1) && $this->isFilter('_primary'))
			{
				$filter_primary = $this->getFilter('_primary');
				$fcount_primary = count($filter_primary);

                foreach ($filter_primary as $key => $value)
				{
					if ($value != 0)
					{
						$tmp_options_primary[] = $key;
						$tmp_values_primary[] = $value;
					}
				}

				for ($i = 0; $i < $pos_count; $i++)
				{
					if ($tmp_values_primary)
					{
						if (in_array($possibleData[$i]['attributes_id'], $tmp_values_primary))
						{
							$possibleProducts_primary[] = $possibleData[$i]['products_id'];
						}
					}
				}
			}
			else
			{
				// default: use all products found in possibleData
				for ($i = 0; $i < $pos_count; $i++)
				{
					$possibleProducts_primary[] = $possibleData[$i]['products_id'];
				}
			}

			// partially superfluous
			for ($i = 0; $i < $pos_count; $i++)
			{
				if (!in_array($possibleData[$i]['attributes_parent_id'], $tmp_options_primary))
				{
					$tmp_options_primary[] = $possibleData[$i]['attributes_parent_id'];
					$tmp_other_values_primary[] = $possibleData[$i]['attributes_id'];
				}
			}

			$option_where_primary = '';
			if ($tmp_options_primary[0])
			{
				// does not seem to make a difference whether this is disabled
				//$option_where_primary.= " and attributes_parent_id IN (".implode(", ", $tmp_options_primary).")";
			}
			else
			{
				$this->slave_no_options=true;
			}

			if (is_array($possibleProducts_primary))
			{
				// array with counts of products
				$_pcount_primary = array_count_values ($possibleProducts_primary);
				$possibleProducts_primary = array_unique($possibleProducts_primary);

				$qry = "select products_id, attributes_id, attributes_parent_id from " . TABLE_PRODUCTS_TO_ATTRIBUTES . "
				where products_id in (".implode(",", $possibleProducts_primary).")".$option_where_primary."";

				$record = $db->CacheExecute($qry);
				if ($record->RecordCount() > 0)
				{
					while (!$record->EOF)
					{
						$option_data_primary[] = $record->fields;
						$record->MoveNext();
					}
					$record->Close();
				}

				$possibleProducts_primary = array();

				$count_data_primary = count($option_data_primary);
				for ($i = 0; $i < $count_data_primary; $i++)
				{

					$possibleOptions_primary[] = $option_data_primary[$i]['attributes_parent_id'];

					if ($fcount_primary == $_pcount_primary[$option_data_primary[$i]['products_id']] || $fcount_primary == 0)
					{
						$possibleProducts_primary[] = $option_data_primary[$i]['products_id'];
						$possibleValues_primary[] = $option_data_primary[$i]['attributes_id'];
					}
				}

				if (is_array($possibleProducts_primary))
				{
					$this->possibleProducts_primary = array_unique($possibleProducts_primary);
				}

				if (is_array($possibleOptions_primary))
				{
					$this->possibleOptions_primary = array_unique($possibleOptions_primary);
				}

				if (is_array($possibleValues_primary))
				{
					$this->possibleValues_primary = array_unique($possibleValues_primary);
				}
			}
		}
		else
		{
			$sdata_sql_products = new getProductSQL_query();
			$sdata_sql_products->setPosition('plugin_ms_getPossibleData_sdata');
			$sdata_sql_products->setSQL_WHERE(" AND p.products_master_model = ?");
			$sdata_sql_products->setSQL_SORT(' p.products_sort ASC');

			$sdata_query = "".$sdata_sql_products->getSQL_query()."";
			$sdata_record = $db->CacheExecute($sdata_query,array($model));

			if ($sdata_record->RecordCount() > 0 || !$this->isFilter())
			{
				while (!$sdata_record->EOF)
				{
					if($sdata_record->fields['products_id'])
					{
						$possibleData[] = $sdata_record->fields;
					}
					$sdata_record->MoveNext();
				}
				$sdata_record->Close();
			}

			$pos_count = count($possibleData);

			for ($i = 0; $i < $pos_count; $i++)
			{
				$possibleProducts_primary[] = $possibleData[$i]['products_id'];
			}

			if (is_array($possibleProducts_primary))
			{
				$this->possibleProducts_primary = array_unique($possibleProducts_primary);
			}
		}

		$this->possibleProducts_primary  = $this->sortPossibleProducts($this->possibleProducts_primary );

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:SetPossible_primary_bottom')) ? eval($plugin_code) : false;
	}

	function getOptionData($optionSet, $optionId)
	{
		foreach($optionSet as $option)
		{
			if($option['id'] == $optionId)
			{
				return $option['data'];
			}
		}
		return false;
	}

	function getOptionDataIds($optionData)
	{
		$ids = array();
		foreach($optionData as $optionValue)
		{
			$ids[] = $optionValue['id'];
		}
		return $ids;
	}

	function disableOptionValue(&$optionSet, $optionId, $valueId)
	{
		foreach($optionSet as $k => $option)
		{
			if($option['id'] == $optionId)
			{
				foreach($option['data'] as $k2 => $value)
				{
					if($value['id'] == $valueId )
					{
						$optionSet[$k]['data'][$k2]['disabled'] = true;
					}
				}
			}
		}
	}

	/**
	 *
	 * Returns dropdown boxes with different options as HTML
	 *
	 * @return string $optionData HTML of options
	 */
	function getOptions()
	{
		global $product, $xtPlugin, $xtLink, $db;

		static $options = null;

		if (!empty($options))
		{
			return $options;
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getOptions_top')) ? eval($plugin_code) : false;
		if (!$this->not_master_slave_pr && !$this->slave_no_options)
		{
			$option_set = $this->buildOptionSet();

			$optionSet_arr_primary = $this->buildOptionSet('primary');
			// build two option sets with possible and all options and then merge them
			$optionSet_arr = $this->buildOptionSet('all');
			//$optionSet_arr = array($optionSet_arr[0]);

            // JW: Löscht die vorletzte Eigenschaft - warum - entfernt
            //			unset($optionSet_arr[count($optionSet_arr)-1]);

			//var_dump($optionSet_arr);
			$mergedOptions_arr = $this->mergeOptions($option_set, $optionSet_arr_primary,$optionSet_arr);

			// use $mergedOptions_arr instead of $option_set
			$selectedCount = 0;
			foreach ($mergedOptions_arr as $k => $v)
			{
				if ($v['selected'] && !empty($v['selected']))
				{
					$selectedCount++;
				}
			}

            // Hier ist der Fix von JW

            foreach($mergedOptions_arr as $k => $option)
            {
                if(empty($option['selected'])){
                    foreach($option['data'] as $k2 => $optionData){
                        if($optionData['id'] != 0 && !in_array($optionData['id'], $this->possibleValues)){
                            $mergedOptions_arr[$k]['data'][$k2]['disabled'] = true;
                        }
                    }
                }
            }


            // Wenn ich gerade dabei bin besorgen wir noch schnell die URL des Masters, dann können wir einen "Auswahl zurücksetzen" - Button einfügen.
            global $store_handler;
            $jw_master_pid = $db->GetOne("select products_id from ".TABLE_PRODUCTS." where products_model = '".$this->master_model."'");
            // Einzelartikel abfangen
            if($jw_master_pid != '' && $_SESSION['selected_language'] != ''){
                $jw_master_url = $db->GetOne("select `url_text` from `".TABLE_SEO_URL."` where `link_type` = 1 and `link_id` = '".$jw_master_pid."' and `language_code` = '".$_SESSION['selected_language']."' and `store_id` = ".$store_handler->shop_id);
                $this->products_master_url = _SYSTEM_BASE_URL."/".$jw_master_url;
            }else{
                //error_log("MasterSlave: Master:".$this->master_model." Store: ".$store_handler->shop_id, 0);
            }
            // Ende Fix, nur noch eine Änderung in Zeile 1141

			// TEXT_XT_MASTER_SLAVE_NO_STOCK
			$tpl_data = array(
				'master_url' => $this->products_master_url, // Eingefügt von JW
				'options' => $mergedOptions_arr,
				'pID' => $this->pID,
				'error_message' => $_SESSION['master_slave_error'],
				'ms_allow_add_cart' => $selectedCount == count($mergedOptions_arr) ? true : false
			);
			unset($_SESSION['master_slave_error']);
			$tpl = _getSingleValue(array('value' => 'products_option_template', 'table' => TABLE_PRODUCTS, 'key' => 'products_model', 'key_val' => $this->master_model));

			if (!$tpl)
			{
				$tpl = 'ms_default.html';
			}
			($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:_getOptions_center')) ? eval($plugin_code) : false;
			// $plugin_return_value: origin unknown, propably undefined
			if (isset($plugin_return_value))
			{
				return $plugin_return_value;
			}
			$template = new Template();
			$template->getTemplatePath($tpl, 'xt_master_slave', 'options', 'plugin');

			$optionData = $template->getTemplate('xt_master_slave_default_smarty', $tpl, $tpl_data);

			$options = $optionData;
			return $options;
		}
	}

	/**
	 *
	 * get array with options
	 *
	 * @return array $oData_array
	 */
	function buildOptionSet($mode_str = 'possible')
	{
		global $xtPlugin, $product, $language, $db;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:buildOptionSet_top')) ? eval($plugin_code) : false;
		// build either all or possible (default) options
		if ($mode_str == 'possible')
		{
			$modeOptions_arr = $this->possibleOptions;
			$modeValues_arr  = $this->possibleValues;
		}
		elseif ($mode_str == 'primary')
		{
			$modeOptions_arr = $this->possibleOptions_primary;
			$modeValues_arr  = $this->possibleValues_primary;
		}
		else
		{
			$modeOptions_arr = $this->allOptions;
			$modeValues_arr  = $this->allValues;
		}

		// instead of:
		// if (!$this->possibleOptions) return;
		if (!is_array($modeOptions_arr) or empty($modeOptions_arr) or !is_array($modeValues_arr) or empty($modeValues_arr))
		{
			return array();
		}

		// options of current product
		$product_option_data = $this->getAttributesData($this->pID);

		$odata = $db->CacheExecute("select distinct pa.*,pad.*, pat.* from " . TABLE_PRODUCTS_ATTRIBUTES . " pa

										left join ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." pad on pa.attributes_id = pad.attributes_id
										LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES_TEMPLATES." pat ON pat.attributes_templates_id = pa.attributes_templates_id
								   where pad.language_code = ? and pa.attributes_id in (".implode(",", $modeOptions_arr).") and
								   pa.status = 1 order by pa.sort_order, pad.attributes_name",
			array($language->code));

		$rows = 0;		// = options
		$cols = 1;		// = values
		if ($odata->RecordCount() > 0)
		{
			while (!$odata->EOF)
			{
				$selected_value = $_SESSION['select_ms'][$this->pID]['id'][$odata->fields['attributes_id']];

				if ($odata->fields['attributes_image'] != '')
				{
					$odata->fields['attributes_image'] = _SRV_WEB_IMAGES . 'org/' . $odata->fields['attributes_image'];
				}
				$oData_array[$rows] = array('id' => $odata->fields['attributes_id'],
					'text' => $odata->fields['attributes_name'],
					'desc' => $odata->fields['attributes_desc'],
					'model'=>$odata->fields['attributes_model'],
					'image'=>$odata->fields['attributes_image'],
					'attributes_templates'=>$odata->fields['attributes_templates_name'],
					'data' => array(),
					'color' => $odata->fields['attributes_color'],
				);

				$vdata = $db->CacheExecute("select distinct pa.*,pad.*, pat.* from " . TABLE_PRODUCTS_ATTRIBUTES . " pa
												left join ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." pad on pa.attributes_id = pad.attributes_id
												LEFT JOIN ".TABLE_PRODUCTS_ATTRIBUTES_TEMPLATES." pat ON pat.attributes_templates_id = pa.attributes_templates_id
										  where pa.attributes_parent = ? and pad.language_code = ? and 
										  pa.attributes_id in (".implode(",", $modeValues_arr).") and pa.status = 1 order by pa.sort_order, pad.attributes_name",
					array((int)$odata->fields['attributes_id'],$language->code));

				if ($vdata->RecordCount() > 0)
				{
					if ($odata->fields['attributes_templates_name']=='select')
					{
						$oData_array[$rows]['data'][0] = array('id' => 0,
							'text' => TEXT_NO_SELECTION,
							'desc' => ''
						);
					}

					while (!$vdata->EOF)
					{
						// check if selected
						if ($vdata->fields['attributes_image'] != '')
						{
							$vdata->fields['attributes_image'] = _SRV_WEB_IMAGES . 'org/' . $vdata->fields['attributes_image'];
						}
                        //$oData_array[$rows]['data'] = array();
						$oData_array[$rows]['data'][$cols] = array('id' => $vdata->fields['attributes_id'],
							'text' => $vdata->fields['attributes_name'],
							'desc' => $vdata->fields['attributes_desc'],
							'text' => $vdata->fields['attributes_name'],
							'model'=>$vdata->fields['attributes_model'],
							'image'=>$vdata->fields['attributes_image'],
							'attributes_templates'=>$vdata->fields['attributes_templates_name'],
							'color' => $vdata->fields['attributes_color'],
						);

						if ($selected_value == $vdata->fields['attributes_id'] || ($vdata->RecordCount() == 1 && !$this->unset))
						{
							// set selected flag only in mode: possible
							if ($mode_str == 'possible')
							{
								$oData_array[$rows]['data'][$cols]['selected'] = true;
							}
						}

						if (isset($product_option_data[$odata->fields['attributes_id']]['attributes_id']))
						{
							if ($product_option_data[$odata->fields['attributes_id']]['attributes_id'] == $vdata->fields['attributes_id'])
							{
								// set selected flag only in mode: possible
								if ($mode_str == 'possible')
								{
									$oData_array[$rows]['data'][$cols]['selected'] = true;
								}
							}
						}

						($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:_buildOptionSet_value')) ? eval($plugin_code) : false;

						$cols ++;
						$vdata->MoveNext();
					}
					$vdata->Close();
				}

				if (!is_array($oData_array[$rows]['data']))
				{
					unset($oData_array[$rows]);
				}
				else
				{
					$rows ++;
				}
				$odata->MoveNext();
			}
			$odata->Close();
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:buildOptionSet_bottom')) ? eval($plugin_code) : false;
		return $oData_array;
	}


	/**
	 * get array with assigned options
	 *
	 * @param mixed $pID products id
	 * @return bool|array $data options of current product or false
	 */
	function getAttributesData($pID)
	{
		global $db,$xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getAttributesData_top')) ? eval($plugin_code) : false;
		$option_data = $db->CacheExecute("select products_id, attributes_id, attributes_parent_id from " . TABLE_PRODUCTS_TO_ATTRIBUTES . "
		                  where products_id = ?",array((int)$pID));
		if ($option_data->RecordCount() == 0)
		{
			return false;
		}
		$data = array();
		while (!$option_data->EOF)
		{
			$data[$option_data->fields['attributes_parent_id']]=$option_data->fields;
			$option_data->MoveNext();
		}
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getAttributesData_bottom')) ? eval($plugin_code) : false;
		return $data;
	}

	/**
	 *
	 * get current options of product with names
	 *
	 * @param int $pID product ID
	 * @return array $data
	 */
	function getAttributesValueData($attributesId)
	{
		global $db, $language,$xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getFullAttributesData_top')) ? eval($plugin_code) : false;
		$option_data = $db->CacheExecute("select *
		                              from  ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." pad where pad.language_code = '" . $language->code . "'
		                              AND pad.attributes_id = ?",array((int)$attributesId));
		if ($option_data->RecordCount() > 0)
		{
			while (!$option_data->EOF)
			{
				$data = $option_data->fields;
				$option_data->MoveNext();
			}
			$option_data->Close();
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getFullAttributesData_bottom')) ? eval($plugin_code) : false;
		return $data;
	}

	/**
	 *
	 * get current options of product with names
	 *
	 * @param int $pID product ID
	 * @return array $data
	 */
	function getFullAttributesData($pID)
	{
		global $db, $language,$xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getFullAttributesData_top')) ? eval($plugin_code) : false;
		$option_data = $db->CacheExecute("select padv.attributes_id as option_id, padv.attributes_name as option_name, pad.attributes_id as option_value_id, pad.attributes_name as option_value_name
		                              from " . TABLE_PRODUCTS_TO_ATTRIBUTES . " pa 
		                              left join ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." pad on (pa.attributes_id = pad.attributes_id and pad.language_code = '" . $language->code . "')
		                              left join ".TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION." padv on (pa.attributes_parent_id = padv.attributes_id and padv.language_code = '" . $language->code . "')
		                              where pa.products_id = ?",array((int)$pID));
		if ($option_data->RecordCount() > 0)
		{
			while (!$option_data->EOF)
			{
				$data[] = $option_data->fields;
				$option_data->MoveNext();
			}
			$option_data->Close();
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getFullAttributesData_bottom')) ? eval($plugin_code) : false;
		return $data;
	}

	function getAllSlaves($slaves_order=false)
	{
		global $db,$xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getAllSlaves_top')) ? eval($plugin_code) : false;
		$all_p = array();

		$sql_where=' and p.products_master_model =? ';

		$sdata_sql_products = new getProductSQL_query();
		$sdata_sql_products->setPosition('plugin_ms_sdata_sql_products_sdata');
		$sdata_sql_products->setSQL_WHERE($sql_where);
		if ($slaves_order)
		{
			$sdata_sql_products->setSQL_SORT('products_master_slave_order '.XT_MASTER_SLAVE_SLAVE_ORDER);
		}
		$sdata_query = $sdata_sql_products->getSQL_query();

		$odata = $db->CacheExecute($sdata_query,array($this->master_model));

		//$db->CacheExecute("select products_id from " . TABLE_PRODUCTS . " p where p.products_master_model ='".$this->master_model."' and p.products_status=1 ". $sql_where);

		if ($odata->RecordCount() > 0)
		{
			while (!$odata->EOF)
			{

				array_push($all_p,$odata->fields['products_id']);
				$odata->MoveNext();
			}
			$odata->Close();
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getAllSlaves_bottom')) ? eval($plugin_code) : false;
		return $all_p;
	}

	function getSlaveOptions($id)
	{
		global $db,$xtPlugin;
		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getSlaveOptions_top')) ? eval($plugin_code) : false;
		$all_p = array();
		$odata = $db->CacheExecute(" SELECT pa.* FROM   " . TABLE_PRODUCTS_TO_ATTRIBUTES." pa where pa.products_id=?",array((int)$id));

		if ($odata->RecordCount() > 0)
		{
			while (!$odata->EOF)
			{

				$data[$odata->fields['attributes_parent_id']] = $odata->fields['attributes_id'];
				array_push($all_p,$data);
				$odata->MoveNext();
			}
			$odata->Close();
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getSlaveOptions_bottom')) ? eval($plugin_code) : false;
		return $all_p;
	}

	/**
	 *
	 * returns slave list as HTML or void, if no match is found
	 *
	 * @param unknown_type $data NOT USED
	 * @return string|void $page_data slave products as HTML or void
	 */
	function getProductList($data = '', $all = false, $slaves_order = false)
	{
		global $xtPlugin, $product, $template, $current_product_id;

		//if(count($this->possibleProducts)==1) return '';

		$this->flag_processingProductList = true;

		$all_p = array();

        $filterList = xt_master_slave_functions::getOverrideSetting(XT_MASTER_SLAVE_FILTERLIST_ON_SELECTION, 'ms_filter_slave_list', $this->pID);

		if($filterList && count($this->possibleProducts)>1)
		{
			foreach($this->possibleProducts as $ps)
			{
			    if($current_product_id == $ps['products_id']) continue;
				$temp_p = product::getProduct($ps['products_id']);
				$all_p[] = $temp_p->data;
			}
		}
		else {
            if(count($this->allProduct_ids)>1)
                $foreach_products = $this->allProduct_ids;
            else
                $foreach_products = $this->possibleProducts;
            foreach($foreach_products as $ps_id)
            {
                $temp_p = product::getProduct($ps_id);
                $all_p[] = $temp_p->data;
            }
		}
		$this->flag_processingProductList = false;

        if($filterList != 1 && count($all_p)>1)
        {
            usort($all_p, function ($a, $b) {
                if ($a['products_master_slave_order'] == $b['products_master_slave_order'])
                {
                    return 0;
                }
                if (constant('XT_MASTER_SLAVE_SLAVE_ORDER') == 'ASC')
                {
                    return ($a['products_master_slave_order'] < $b['products_master_slave_order']) ? -1 : 1;
                }

                return ($a['products_master_slave_order'] > $b['products_master_slave_order']) ? -1 : 1;
            });
        }

		$tpl_data = array('product_listing' => $all_p);

		$tpl = _getSingleValue(array('value'=>'products_option_list_template', 'table'=>TABLE_PRODUCTS, 'key'=>'products_model', 'key_val'=>$this->master_model));

		if(!$tpl)
		{
			$tpl = 'ms_product_list_default.html';
		}

		($plugin_code = $xtPlugin->PluginCode('class.xt_master_slave_products.php:getProductList_center')) ? eval($plugin_code) : false;
		// $plugin_return_value: origin unknown, propably undefined
		if(isset($plugin_return_value))
		{
			return $plugin_return_value;
		}

		$template = new Template();

		$template->getTemplatePath($tpl, 'xt_master_slave', 'product_listing', 'plugin');

		$page_data = $template->getTemplate('xt_master_slave_smarty', $tpl, $tpl_data);

		define('_PLUGIN_MASTER_SLAVE_SHOW_SLAVE_LIST_SHOW',1);
		return $page_data;
	}


	/**
	 *
	 * sort possible products by products_sort
	 *
	 * @param array $data product IDs
	 * @return array sorted product IDs
	 */
	public function sortPossibleProducts($data)
	{
		global $db;
		if (is_array($data) and !empty($data))
		{
			$query = 'SELECT products_id FROM '.TABLE_PRODUCTS . ' WHERE products_id IN ('.implode(',',$data).') ORDER BY products_sort';
			$res = $db->getAll($query);
			if (!empty($res))
			{
				$pids = array();
				foreach ($res as $p)
				{
					$pids[] = 	$p['products_id'];
				}
				return $pids;
			}
			else
			{
				return $data;
			}
		}
		return $data;
	}

	public function sortPossibleProductsByMsOrder($data, $dir = 'ASC')
	{
		global $db;
		if (is_array($data) and !empty($data))
		{
			$query = 'SELECT products_id FROM '.TABLE_PRODUCTS . ' WHERE products_id IN ('.implode(',',$data).') ORDER BY products_master_slave_order '.$dir;
			$res = $db->getAll($query);
			if (!empty($res))
			{
				$pids = array();
				foreach ($res as $p)
				{
					$pids[] = 	$p['products_id'];
				}
				return $pids;
			}
			else
			{
				return $data;
			}
		}
		return $data;
	}


	public function allOptionsSelected()
	{
		$requiredKeys = array_values($this->possibleOptions);

		if(is_array($_SESSION['select_ms'])
			&& is_array($_SESSION['select_ms'][$this->pID])
			&& count(array_intersect_key(array_flip($requiredKeys), $_SESSION['select_ms'][$this->pID]['id'])) === count($requiredKeys)
		)
		{
			return true;
		}
		return false;
	}

}
