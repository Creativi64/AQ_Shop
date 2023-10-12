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


class default_table extends xt_backend_cls {

	
	/**
	 * default get function
	 *
	 * @param int $ID
	 * @return object
	 */
	function _get($ID = 0) {
		global $xtPlugin, $db, $language;

        $obj = new stdClass;

		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}

		$table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);

		if ($this->url_data['get_data'])
		$data = $table_data->getData();
		elseif($ID)
		$data = $table_data->getData($ID);
		else
		$data = $table_data->getHeader();

		$obj = new stdClass;
		$obj->totalCount = count($data);
		$obj->data = $data;

		return $obj;
	}

	/**
	 * default set function
	 *
	 * @param array $data
	 * @param string $set_type
	 * @return object
	 */
	function _set($data, $set_type='edit'){
		global $db,$language,$filter;
		
		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();
			
		return $obj;
	}

	/**
	 * default unset function
	 *
	 * @param int $id
	 * @return unknown
	 */
	function _unset($id = 0) {
		global $db;
		if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;

		$db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));
	}

}