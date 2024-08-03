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

class system_status extends xt_backend_cls {

	protected $_table = TABLE_SYSTEM_STATUS;
	protected $_table_lang = TABLE_SYSTEM_STATUS_DESCRIPTION;
	protected $_table_seo = null;
	protected $_master_key = 'status_id';

	public $values;
	public $status_fields_array;

	function __construct($export_lang='') {
		global $db,$language;
		
		if ($export_lang!='') $lng = $export_lang;
		elseif(is_object($language)) $lng = $language->code;
		elseif (isset($_SESSION["selected_language"])) $lng = $_SESSION["selected_language"];
		else if(defined('_STORE_LANGUAGE')) $lng =_STORE_LANGUAGE;
		else {
		    foreach(['de','en','fr'] as $code)
            {
                $lng = $db->GetOne("SELECT ? FROM ".TABLE_LANGUAGES.' WHERE code = ?', [$code,$code]);
                if($lng) break;
            }
		    if(empty($lng))
                $lng = $db->GetOne("SELECT code FROM ".TABLE_LANGUAGES.' LIMIT 0,1'); // fallback
        }
		
		$this->values = array ();
		$result = $db->CacheExecute(
			"SELECT * FROM " . $this->_table . " st, ".$this->_table_lang." std where st.status_id=std.status_id and std.language_code = ?",
			array($lng)
		);

		$classes = array();

		while (!$result->EOF) {
            $s = stripslashes($result->fields['status_values'] ?: '');
			$arr = unserialize($s);

			$data = array (
				'id' => $result->fields['status_id'],
				'name' => $result->fields['status_name'],
				'image' => $result->fields['status_image']
			);

			if (is_array($arr))
			{
			    $data = array_merge($data,$arr);
            }
			if (!isset($classes[$result->fields['status_class']]))
			{
			    if(isset($arr['sorting']))
                {
                    $classes[$result->fields['status_class']] = $arr['sorting'];
                }
                else if (isset($arr['data']['sorting']))
                {
                    $classes[$result->fields['status_class']] = $arr['data']['sorting'];
                }
            }

			$this->values[$result->fields['status_class']][$result->fields['status_id']] = $data;
			$result->MoveNext();
		}

		// sort array
		foreach ($classes as $key => $val) {
			$this->values[$key] = $this->matrixSort($this->values[$key],$val);
		}
	}

	function matrixSort(&$matrix,$sortKey,$sort = 'ASC') {
		if (count($matrix) == 0) return false;

		foreach($matrix as $key => $subMatrix) {
			$tmpArray[$key]=$subMatrix['data'][$sortKey];
		}
		arsort($tmpArray);

        $ArrayNew = array();
        foreach($tmpArray as $key => $value) {
		    $ArrayNew[$key]=$matrix[$key];
		}

		if ($sort != 'ASC') {
			$ArrayNew = array_reverse($ArrayNew);
		}

		return $ArrayNew;
	}

	function _getSingle($search_type, $search_key, $search_value, $return_value='all'){
		$content_data = $this->values[$search_type];
        foreach($content_data as $key => $value) {
			if(array_key_exists($search_key, $value) && $value[$search_key]==$search_value){
				if($return_value!='all'){
				 	return	$value[$return_value];
				}else{
					return	$value;
				}
			}
		}
	}

	function _buildArray($data){
		global $db, $language;

		$t_fields = getTableFields($this->_table);
		$t_lang_fields = getTableFields($this->_table_lang);
        $field_array = [];

		foreach ($t_fields as $key => $val) {
			$field_array[] = $key;
		}

		foreach ($t_lang_fields as $key => $val) {
			foreach ($language->_getLanguageList() as $lkey => $lval) {
				if($key !='status_id')
				$field_array[] = $key.'_'.$lval['code'];
			}
		}

		foreach ($field_array as $key => $val) {
			$new_data[$val] = $data[$val];
			unset($data[$val]);
		}

		$tmp_data['data'] = $data;

		$new_data['status_values'] = addslashes(serialize($tmp_data));

		return $new_data;
	}

	function _defaultValues(){
		global $xtPlugin;

		$data_array = array('stock_rule','shipping_status','base_price','order_status','campaign','zone');

		($plugin_code = $xtPlugin->PluginCode('class.system_status.php:_defaultValues_bottom')) ? eval($plugin_code) : false;

        $new_data_array = [];
		foreach ($data_array as $key => $val) {
			$new_data_array[$val] = $val;
		}

		return $new_data_array;

	}

	function _completeData($data){

		if($data['status_values'] != ''){
			
			$tmp_arr_data = preg_replace('%/%','',$data['status_values']);
			$tmp_arr_data = str_replace('/','',$tmp_arr_data);
			
			$tmp_arr_data = stripslashes($tmp_arr_data);
			$arr = unserialize(stripslashes($tmp_arr_data));
	
			if(!is_array($arr))
			{
			$arr = unserialize(stripslashes($this->status_fields_array));
			}
			else if(is_array($arr['data']))
			{
				$arr2 = unserialize(stripslashes($this->status_fields_array));
				if(is_array($arr2['data']))
				{
					$arr['data'] = array_merge($arr2['data'], $arr['data']);
				}
			}

            // xt 6.2.0 update stock_rule add mapping_schema_org
            if($this instanceof stock_rule
                && !array_key_exists("mapping_schema_org", $arr["data"]))
            {
                $arr["data"]["mapping_schema_org"] = 0;
            }

				if(is_array($arr)){
					foreach ($arr as $key => $val) {

						if(is_array($val)){
							foreach ($val as $dkey => $dval) {
								$data[$dkey] = $dval;
							}
						}else{
								$data[$key] = $val;
						}

					}
				}

			unset($data['status_values']);
		}

		return $data;
	}

    function _defineFields(){
        $this->status_fields_array = "";
    }

	function _get($ID = 0) {
		global $xtPlugin, $db, $language;

		$this->_defineFields();

		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
               $obj = $this->_set(array(), 'new');
               $ID = $obj->new_id;
		}

		$where = 'status_class = "'.$this->_master_status.'"';

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where);

		if ($this->url_data['get_data']){
			$data = $table_data->getData();
		}elseif($ID){
			$data = $table_data->getData($ID);

			if($this->status_fields_array){
			$data[0] = $this->_completeData($data[0]);
			}else{
			unset($data[0]['status_values']);
			}
		}else{
			$data = $table_data->getHeader();
		}

		$obj = new stdClass;
        $obj->totalCount = count($data);
        $obj->data = $data;

        return $obj;
	}

	function _set($data, $set_type = 'edit'){
		global $db,$language,$filter;

		 $obj = new stdClass;

		 $this->_defineFields();

		 $tmp_data = $data;
		 unset($data);

		foreach ($tmp_data as $key => $val) {

			if($val == 'on')
			   $val = 1;

			if($key!='')
			$data[$key] = $val;
		}

		 if ($set_type=='edit') {
		 	
             // take default
             if($this->status_fields_array) {
            $default_values = unserialize(stripslashes($this->status_fields_array));
            foreach ($default_values['data'] as $key => $val) { 
               $default_values['data'][$key]=0; 
            }    
            $data = array_merge($default_values['data'],$data);
             
			 $data = $this->_buildArray($data);
             }
		 }

		 if ($set_type=='new') {

		 	$record = $db->Execute("SELECT max(status_id) as id from " . $this->_table . "");
			$new_id = $record->fields['id']+1;

			$data['status_id'] = $new_id;

		 	if($this->status_fields_array)
			$data['status_values'] = $this->status_fields_array;

		 	$data['status_class'] = $this->_master_status;
		 }

		 $oCS = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		 $objCS = $oCS->saveDataSet();

		 if ($set_type=='new') {	// edit existing
			 $obj->new_id = $new_id;
			 $data = array_merge($data, array($this->master_key=>$obj->new_id));
		 }

		 $oCSD = new adminDB_DataSave($this->_table_lang, $data, true, __CLASS__);
		 $objCSD = $oCSD->saveDataSet();

		 if ($objCS->success && $objCSD->success) {
		     $obj->success = true;
		 } else {
		     $obj->failed = true;
		 }

		return $obj;
	}

	function _unset($id = 0) {
	    global $db;
	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if (!is_int($id)) return false;

	    $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));
	    $db->Execute("DELETE FROM ". $this->_table_lang ." WHERE ".$this->_master_key." = ?", array($id));

	}
	
	function getSingleValue($id,$lang='')
	{
		global $db,$language;
		$data=array();
		if ($lang=='') $lang = $language->code;
		
		$record =$db->Execute(
			"Select * FROM  ". $this->_table_lang ." WHERE status_id =? and language_code=?",
			array($id, $lang)
		);
		
		if($record->RecordCount() >0)
		{
			$data = $record->fields;
		}
		
		return $data;
	}

}