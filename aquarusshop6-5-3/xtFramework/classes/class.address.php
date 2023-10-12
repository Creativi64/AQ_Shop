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

class address extends customer{

	public $_table = TABLE_CUSTOMERS_ADDRESSES;
	public $_table_lang = null;
	public $_table_seo = null;
	public $_master_key = 'address_book_id';

	function setPosition ($position) {
		$this->position = $position;
	}

	function _getParams() {
		$params = array();

		// fix refresh tab function
		if($this->url_data['pg']=='overview' && empty($_REQUEST['parentNode']))
		{
			$_REQUEST['parentNode'] = 'customerAddresses_'.$this->url_data['adID'];
		}

		$header['customers_dob'] = array('type' => 'date');
		$header['customers_id'] = array('type' => 'hidden');
		$header['address_book_id'] = array('type' => 'hidden');

		$header['customers_country_code'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=countries'
		);

		$header['address_class'] = array(
			'type' => 'dropdown',
			'url'  => 'DropdownData.php?get=address_types'
		);

		$header['customers_gender'] = array(
			'renderer' => 'genderRenderer','type' => 'dropdown',
			'url'  => 'DropdownData.php?get=gender'
		);
		

		$header['date_added'] = array(
			'type' => 'hidden', 'readonly' => false
		);
		$header['last_modified'] = array(
			'type' => 'hidden', 'readonly' => false
		);
		
		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
 		$params['languageTab']    = false;
		$params['edit_masterkey'] = false;

		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array (
				'address_book_id',
				'customers_gender',
				'customers_company',
				'customers_firstname',
				'customers_lastname',
				'customers_postcode',
				'customers_city',
				'customers_postcode',
				'customers_street_address'
			);
		}else{
			$params['exclude'] = array(/*'date_added', 'last_modified',*/'external_id');
		}

		return $params;
	}

	function _get($ID = 0, $searched = '') {
		global $xtPlugin, $db, $language;

		if ($this->position != 'admin') return false;

		$obj = new stdClass();

		if ($ID === 'new') {
               $obj = $this->_set(array(), 'new');
               $ID = $obj->new_id;
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, 'customers_id='.(int)$this->url_data['adID']);
		if ($this->url_data['get_data']){
			$data = $table_data->getData();
		}elseif($ID){
			$data = $table_data->getData($ID);
			if(is_array($data))
            {
                $clear_data = false;
                if(!empty($data[0]['customers_federal_state_code']))
                {
                    $states_code = $db->GetOne('SELECT states_code FROM ' . TABLE_FEDERAL_STATES . ' WHERE states_id=?', [$data[0]['customers_federal_state_code']]);
                    if ($states_code)
                    {
                        $data[0]['customers_federal_state_code'] = $states_code;
                    }
                    else $clear_data = true;
                }
                else $clear_data = true;
                if($clear_data)
                {
                    $db->Execute('UPDATE '.TABLE_CUSTOMERS_ADDRESSES.' SET customers_federal_state_code = NULL WHERE address_book_id = ?', [$ID]);
                }

            }
		}else{
			$data = $table_data->getHeader();
		}

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

		return $obj;
	}

	function _set($data, $set_type = 'edit') {
		global $db,$language,$filter;

		if($set_type=='new'){
			$data['customers_id'] = (int)$this->url_data['adID'];
		}

		$states_code = $data['customers_federal_state_code'];
		$states_id = (int) $data['customers_federal_state_code'];
        if(is_array($data)
            && (!empty($states_code) || $states_id)
            && !empty($data['customers_country_code']))
        {
            if($states_id)
            {
                $states_id = $db->GetOne('SELECT states_id FROM '. TABLE_FEDERAL_STATES.' WHERE states_id=? AND country_iso_code_2=?',
                    [$states_id, $data['customers_country_code']]);
            }
            else {
                $states_id = $db->GetOne('SELECT states_id FROM '. TABLE_FEDERAL_STATES.' WHERE states_code=? AND country_iso_code_2=?',
                    [$states_code, $data['customers_country_code']]);
            }
            if(empty($states_id))
            {
                $obj = new stdClass();
                $obj->success = false;
                $obj->error_message = 'Bundesland '.$data['customers_federal_state_code'].' nicht gefunden für '.$data['customers_country_code']."<br/>"
                    .'Federal state '.$data['customers_federal_state_code'].' not found for '.$data['customers_country_code'];
                return $obj;
            }

            $data['customers_federal_state_code'] = $states_id;
        }
        if($data['customers_federal_state_code'] == 0)
        {
            $data['customers_federal_state_code'] = null;
        }

		 $obj = new stdClass;
		 $o = new adminDB_DataSave(TABLE_CUSTOMERS_ADDRESSES, $data, false, __CLASS__);
		 $obj = $o->saveDataSet();

		return $obj;
	}

	function _unset($id = 0) {
	    global $db;

	    if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if (!is_int($id)) return false;

        // customer_id
        $cid = $db->GetOne("SELECT customers_id FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", [$id]);

	    $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));

        // gibt es noch eine default
        $found = $db->GetOne("SELECT 1 FROM ". $this->_table ." WHERE customers_id = ? AND address_class in ('default')", [$cid]);
        if (!$found)
        {
            // wenn nicht, wird der neueste Eintrag auf default gesetzt
            $rows = $db->GetArray("SELECT * FROM " . $this->_table. " WHERE customers_id = ? order by address_book_id DESC", [$cid]);
            if(count($rows))
                $db->Execute("UPDATE ". $this->_table ." SET address_class = 'default' WHERE ".$this->_master_key." = ?", [$rows[0]['address_book_id']]);
        }
	}
}