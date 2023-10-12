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

class countries extends xt_backend_cls{

	public $default_country = _STORE_COUNTRY;
	public $countries_list = array();
	public $countries_list_sorted = array();
	public $phone_delimiter = "-";

	protected $_table = TABLE_COUNTRIES;
	protected $_table_lang = TABLE_COUNTRIES_DESCRIPTION;
	protected $_table_seo = null;
	protected $_master_key = 'countries_iso_code_2';


	function __construct($status=true,$list_type=''){
		global $xtPlugin;
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'countries_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;		
		
		$this->getPermission();
		$this->countries_list = $this->_buildCountryList($status,$list_type);
		$this->countries_list_sorted = $this->countries_list; //$this->_sortList('countries_name');
		
		
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'countries_bottom')) ? eval($plugin_code) : false;
		
	}
	
	function getPermission(){
		global $store_handler, $customers_status, $xtPlugin;

		$this->perm_array = array(
			'shop_perm' => array(
				'type'=>'shop',
				'table'=>TABLE_COUNTRIES_PERMISSION,
				'key'=>$this->_master_key,
				'pref'=>'c'
			)
		);

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':getPermission')) ? eval($plugin_code) : false;

		$this->permission = new item_permission($this->perm_array);

		return $this->perm_array;
	}
	
	function _buildCountriesPhonePrefix($status=true,$list_type=''){
        global $db,$xtPlugin,$language;
        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountriesPhonePrefix_top')) ? eval($plugin_code) : false;
        $data = array(array("id"=>"","text"=>__text('TEXT_CHOOSE_PHONE_PREFIX')));
        if($status == true){
			$sql_qry = " and c.status = 1";
		}
             
        if($list_type=='store'){
            $table = $this->permission->_table;
            $where = $this->permission->_where;
            $sql_qry .= " and c.phone_prefix != '' ";
        }
        $qry = "SELECT * FROM " . TABLE_COUNTRIES." c LEFT JOIN ".TABLE_COUNTRIES_DESCRIPTION." cd ON c.countries_iso_code_2=cd.countries_iso_code_2 ".$table." 
                WHERE cd.language_code=? ".$sql_qry.$where;
       
        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountriesPhonePrefix_after_qry')) ? eval($plugin_code) : false;

        $record = $db->Execute($qry, array($language->code));
        if($record->RecordCount() > 0){
            while(!$record->EOF){
                $save["id"] = $record->fields['phone_prefix'];
                $save['text'] = $record->fields['countries_name'].'('.$record->fields['phone_prefix'].')';
                $expl = explode(",",$record->fields['phone_prefix']);
                if (count($expl)>1){
                    for($i=0;$i<count($expl);$i++){
                        $save["id"] = $expl[$i];
                        $save['text'] = $record->fields['countries_name'].'('.$expl[$i].')';
                        array_push($data,$save);
                    }
                }else{
                    array_push($data,$save);
                }
                
                ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountriesPhonePrefix_data')) ? eval($plugin_code) : false;
                
                $record->MoveNext();
            }$record->Close();

            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountriesPhonePrefix_bottom')) ? eval($plugin_code) : false;

            return $data;
        }else{
            return false;
        }       
    }
	function _buildCountryList($status=true,$list_type=''){
		global $db, $language, $xtPlugin, $store_handler;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountryList_top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
        return $plugin_return_value;

        $cache_settings = [
            'status' => $status,
            'type' => $list_type,
            'shop' => $store_handler->shop_id,
            'lng' => $language->code
        ];
        if(constant('_USE_CACHE_COUNTRIES') == true)
        {
            $data = xt_cache::getCache('countries', $cache_settings);
            if(is_array($data)) return $data;
        }

        if($status == true){
            $sql_qry = " and c.status = 1 ";
        }

        $perm_data = '';
        if($list_type=='store'){
            $table = $this->permission->_table;
            $where = $this->permission->_where;
            $perm_data = ', fs.*';
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountryList_before_qry')) ? eval($plugin_code) : false;

        $qry2 ="
                SELECT c.*, 
                cd.countries_name,
                fs.states_id, fs.states_code, fs.status as states_status,
                fsd.state_name
                {$perm_data}
                FROM ".TABLE_COUNTRIES." c 
                {$table}
                LEFT JOIN ".TABLE_COUNTRIES_DESCRIPTION." cd ON c.countries_iso_code_2=cd.countries_iso_code_2  
                LEFT JOIN ".TABLE_FEDERAL_STATES." fs ON c.countries_iso_code_2=fs.country_iso_code_2  
                LEFT JOIN ".TABLE_FEDERAL_STATES_DESCRIPTION." fsd ON fs.states_id = fsd.states_id
                WHERE cd.language_code=? 
                {$sql_qry}
                {$where}
                ORDER BY cd.countries_name, fsd.state_name ASC
			";

        $arr = $db->CacheGetArray($qry2, array($language->code));

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountryList_after_qry')) ? eval($plugin_code) : false;

        $data = array();
        foreach ($arr as $iso_2 => $country_entry)
        {
            if(!empty($country_entry['states_code']))
            {
                if(!array_key_exists($country_entry['countries_iso_code_2'], $data))
                {
                    $data[$country_entry['countries_iso_code_2']] = $country_entry;
                }
                if(!empty($country_entry['states_status']))
                {
                    $data[$country_entry['countries_iso_code_2']]['has_federals'] = 1;
                    // daten konform altem muster
                    $data[$country_entry['countries_iso_code_2']]['federal_states'][$country_entry['states_code']] = [
                        'states_id' => $country_entry['states_id'],
                        'states_code' => $country_entry['states_code'],
                        'country_iso_code_2' => $country_entry['countries_iso_code_2'],
                        'status' => $country_entry['states_status'],
                        'language_code' => $language->code,
                        'state_name' => $country_entry['state_name'],
                        'id' => $country_entry['states_id'],
                        'text' => $country_entry['state_name'],
                    ];
                }
            }
            else
            {
                $data[$country_entry['countries_iso_code_2']] = $country_entry;
                $data[$country_entry['countries_iso_code_2']]['has_federals'] = 0;
            }

            // daten konform altem muster
            $unset_keys = ['states_id','states_code','states_status','state_name'];
            foreach($unset_keys as $k)
            {
                unset($data[$country_entry['countries_iso_code_2']][$k]);
            }
            $data[$country_entry['countries_iso_code_2']]['id'] = $country_entry['countries_iso_code_2'];
            $data[$country_entry['countries_iso_code_2']]['text'] = $country_entry['countries_name'];
        }
        unset($arr);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_buildCountryList_data_bottom')) ? eval($plugin_code) : false;

        if(constant('_USE_CACHE_COUNTRIES') == true)
        {
            xt_cache::setCache($data, 'countries', $cache_settings);
        }

        return $data;
	}

	function _getCountryData($country_code){
		global $db, $language, $xtPlugin,$filter;

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_getCountryData_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
			return $plugin_return_value;

		$record = $db->Execute(
			"SELECT * FROM " . TABLE_COUNTRIES." c, ".TABLE_COUNTRIES_DESCRIPTION." cd where c.countries_iso_code_2 = cd.countries_iso_code_2 and cd.language_code=? and c.countries_iso_code_2 = ? ",
			array($language->code, $country_code)
		);

		if($record->RecordCount() > 0){
			while(!$record->EOF){
				$data = $record->fields;
						
				($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_getCountryData_data')) ? eval($plugin_code) : false;

				$record->MoveNext();
			}$record->Close();

			return $data;
		}else{
			return false;
		}
	}

	function _sortList($sort_key='countries_name', $sort_type='ASC'){

		$data = $this->matrixSort($this->countries_list, $sort_key, $sort_type);
        if (!is_array($data)) return;

        $step_1_data = array();
		foreach ($data as $key => $value){
			//$step_1_data[$value['sort_group']][] = $value;
            // there is no sort_group in value
            $step_1_data[0][] = $value;
		}

		$tmp_step_1_data = $step_1_data;

        $step_2_data = array();
        foreach ($tmp_step_1_data as $key => $value){
			$step_2_data[$key] = $this->matrixSort($value, 'countries_name', $sort_type);
		}

		$tmp_step_2_data = $step_2_data;

        $new_data = array();
		foreach($tmp_step_2_data as $key => $value){
			$tmp_step_3_data = $value;
			foreach ($tmp_step_3_data as $key => $value){
				$new_data[] = $value;
			}
		}
		return $new_data;
	}

	function matrixSort(&$matrix,$sortKey,$sort = 'ASC') {
		if (!is_array($matrix) || count($matrix) == 0) return false;

	  	foreach($matrix as $key => $subMatrix)
	  	{
            $tmpArray[$key] = $subMatrix[$sortKey];
	  	}

		arsort($tmpArray);

        $ArrayNew = array();
	    foreach($tmpArray as $key => $value)
			$ArrayNew[$key]=$matrix[$key];

		if ($sort == 'ASC')
			$ArrayNew = array_reverse($ArrayNew);

	   return $ArrayNew;
	 }

	function _getParams() {
		global $db, $language, $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_getParams_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		$params = array();

		$header['countries_iso_code_3'] = array('max' => '3','min'=>'3');
		$header['zone_id'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=tax_zones'
		);

		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = 'countries_name_'.$language->code;

		$params['display_newBtn'] = false;
        $params['display_deleteBtn'] = false;
		$params['display_checkCol']  = true;
		$params['display_statusTrueBtn']  = true;
		$params['display_statusFalseBtn']  = true;
		
		$params['display_searchPanel']  = true;
		$params['RemoteSort']   = true;

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_getParams_bottom')) ? eval($plugin_code) : false;

		return $params;
	}
	
    function _getSearchIDs($search_data) {
        global $filter;

        $sql_tablecols = array('countries_iso_code_2','countries_iso_code_3');

        foreach ($sql_tablecols as $tablecol) {
               $sql_where[]= "(".$tablecol." LIKE '%".$filter->_filter($search_data)."%')";
        }

        if(is_array($sql_where)){
            $sql_data_array = " (".implode(' or ', $sql_where).")";
        }

        return $sql_data_array;
    }

	function _get($ID = 0) {
		global $xtPlugin, $db, $language,$filter;
		$obj = new stdClass;
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_get_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		if ($this->position != 'admin') return false;

		if (!$ID && !isset($this->sql_limit)) {
			$this->sql_limit = "0,25";
		}	

		$where = '';
		if($this->url_data['query']){
            $sql_where = $this->_getSearchIDs($this->url_data['query']);
            $where .= $sql_where;
            $search_keywords = $this->url_data['query'];
            
            //get where query of table_lang
            $c_record = $db->Execute("SELECT countries_iso_code_2 FROM ".TABLE_COUNTRIES_DESCRIPTION ." WHERE countries_name LIKE '%".$search_keywords."%' GROUP BY countries_iso_code_2");
            $l_where = array();
			while(!$c_record->EOF){
    			$l_where[] = $c_record->fields['countries_iso_code_2'];
    			$c_record->MoveNext();
			}$c_record->Close();
			
			$where .= " OR countries_iso_code_2 IN ('".implode('\',\'', $l_where)."')";
        }

        $sort_col = $this->_master_key;
        if (!empty($this->url_data['sort']))
        {
            $data_read = new adminDB_DataRead($this->_table, '', '', 'products_id');
            $fields = $data_read->getTableFields($this->_table);

            if (isset($fields[$this->url_data['sort']]))
            {
                switch ($this->url_data['sort'])
                {
                    default:
                        $sort_col = $this->url_data['sort'];
                        ($plugin_code = $xtPlugin->PluginCode(__CLASS__ . ':_get_sort_qry')) ? eval($plugin_code) : false;
                }
            }
        }
        $sort_dir = (!empty($this->url_data['dir']) &&  $this->url_data['dir'] == 'ASC') ? 'ASC' : 'DESC';
        $sort_order =' ORDER BY '.$this->_table.'.'.$sort_col.' '.$sort_dir;


        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_get_qry')) ? eval($plugin_code) : false;

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where, $this->sql_limit, $this->perm_array, '', $sort_order);

		if ($this->url_data['get_data']){
        	$data = $table_data->getData();
		}elseif($ID){
        	$data = $table_data->getData($ID);
			$data[0]['shop_permission_info']=_getPermissionInfo();
			foreach ($language->_getLanguageList() as $key => $val) {
				$data[0]['countries_name_'.$val['code']] = _filterText($data[0]['countries_name_'.$val['code']]);
			}

        }else{
			$data = $table_data->getHeader();
        }

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_get_bottom')) ? eval($plugin_code) : false;

		if($table_data->_total_count!=0 || !$table_data->_total_count)
			$count_data = $table_data->_total_count;
		else
			$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;

	}

	function _set($data, $set_type='edit'){
		global $db,$language,$filter,$xtPlugin;

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_set_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

		 $obj = new stdClass;

		 $oC = new adminDB_DataSave(TABLE_COUNTRIES, $data);
		 $objC = $oC->saveDataSet();

		 $oCD = new adminDB_DataSave(TABLE_COUNTRIES_DESCRIPTION, $data, true);
		 $objCD = $oCD->saveDataSet();
		
		
		 $set_perm = new item_permission($this->perm_array);
		 
		 $set_perm->_saveData($data, $data[$this->_master_key]);	
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_set_bottom')) ? eval($plugin_code) : false;

		 if ($objC->success && $objCD->success) {
		     $obj->success = true;
		 } else {
		     $obj->failed = true;
		 }

		 xt_cache::deleteCache('countries');

		return $obj;
	}

	function _unset($id = 0) {
		global $xtPlugin;

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_unset_top')) ? eval($plugin_code) : false;
		if(isset($plugin_return_value))
		return $plugin_return_value;

	    return false;
        global $db;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;

	    $db->Execute("DELETE FROM ". TABLE_COUNTRIES ." WHERE ".$this->_master_key." = ?", array($id));
	    $db->Execute("DELETE FROM ". TABLE_COUNTRIES_DESCRIPTION ." WHERE ".$this->_master_key." = ?", array($id));
		
		$set_perm = new item_permission($this->perm_array);
		$set_perm->_deleteData($id);
		
	    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_unset_bottom')) ? eval($plugin_code) : false;

        xt_cache::deleteCache('countries');
	}

	function _setStatus($id, $status) {
		global $db,$xtPlugin;

		$db->Execute("update " . TABLE_COUNTRIES . " set status = ".$status." where countries_iso_code_2 = ?", array($id));

        xt_cache::deleteCache('countries');
	}
}