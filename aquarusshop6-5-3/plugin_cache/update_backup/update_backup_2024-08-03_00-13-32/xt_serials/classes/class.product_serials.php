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


class product_serials extends xt_backend_cls {

	protected $_table = TABLE_PRODUCTS_SERIAL;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'serial_id';

	function setPosition ($position) {
		$this->position = $position;
	}

	function assignSerials($orders_id) {
		global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

		$orders_id = (int)$orders_id;

		$rs = $db->Execute(
			"SELECT p.products_serials,p.products_id,op.orders_products_id,op.products_quantity FROM ".TABLE_PRODUCTS." p, ".TABLE_ORDERS_PRODUCTS." op WHERE p.products_id=op.products_id and op.orders_id=?",
			array($orders_id)
		);
		while (!$rs->EOF) {
			if ($rs->fields['products_serials']=='1') {
				// check if already attached serial
				if (!$this->_hasSerial($orders_id,$rs->fields['orders_products_id'])) {
                    ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':setSerial')) ? eval($plugin_code) : false;
					$this->_setSerial($rs->fields['products_id'],$rs->fields['orders_products_id'],$orders_id,(int)$rs->fields['products_quantity']);
				}
			}
			$rs->MoveNext();
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
	}

	/**
	 * check if enough serials are in stock, assign free serial to product
	 *
	 * @param int $products_id
	 * @param int $orders_products_id
	 * @param int $orders_id
	 */
	function _setSerial($products_id,$orders_products_id,$orders_id,$qty=1) {
		global $db,$logHandler, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

		$rs = $db->Execute(
			"SELECT count(*) as count FROM ".$this->_table." WHERE orders_id='0' and orders_products_id='0' and status='1' and products_id=?",
			array((int)$products_id)
		);
		if ($rs->fields['count'] < constant('XT_SERIALS_WARNING_MIN')) {
			// escalate log
			$log_data = array();
			$log_data['message'] = 'low serial stock';
			$log_data['stock_left'] = $rs->fields['count'];
			$logHandler->_addLog('warning','xt_serials',$products_id,$log_data);
		}

		// get free serials
		$i = 0;
		while ($i<$qty) {
		$rs = $db->Execute(
			"SELECT serial_id FROM ".$this->_table." WHERE orders_id='0' and orders_products_id='0' and status='1' and products_id=? LIMIT 0,1",
			array((int)$products_id)
		);
		$i++;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':freeSerials')) ? eval($plugin_code) : false;

		if ($rs->RecordCount()==0) {
			$log_data = array();
			$log_data['message'] = 'EMPTY serial stock';
			$log_data['stock_left'] = 0;
			$logHandler->_addLog('error','xt_serials',$products_id,$log_data);
		} else {
			$update_array= array();
			$update_array['orders_id']=(int)$orders_id;
			$update_array['orders_products_id']=(int)$orders_products_id;
			$db->AutoExecute($this->_table,$update_array,'UPDATE',"serial_id=".$db->Quote($rs->fields['serial_id']));
		}
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
	}

	/**
	 * check if product has allready assigned serial number
	 *
	 * @param int $orders_id
	 * @param int $orders_products_id
	 * @return boolean
	 */
	function _hasSerial($orders_id,$orders_products_id) {
		global $db;
		$rs = $db->Execute(
			"SELECT * FROM ".$this->_table." WHERE orders_id=? and orders_products_id=?",
			array((int)$orders_id, (int)$orders_products_id)
		);
		if ($rs->RecordCount()>=1) return true;
		return false;

	}

    /**
     * get assigned serial number, otherwise return false
     *
     * @param int|string $orders_id
     * @param int|string $orders_products_id
     * @return false|string
     */
    function _getSerial($orders_id, $orders_products_id)
    {
        global $db;
        return $db->GetOne(
            "SELECT serial_number FROM ".$this->_table." WHERE orders_id=? and orders_products_id=?",
                [(int)$orders_id, (int)$orders_products_id]);
    }

	function getSerialsAdmin($orders_id) {

        global $db, $xtPlugin;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':top')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;


        $html = "";

        $data = [];
        $rs = $db->Execute(
            "SELECT op.*, ps.* FROM ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS_SERIAL." ps WHERE op.orders_id=ps.orders_id and op.orders_products_id=ps.orders_products_id and op.orders_id=?",
            array($orders_id)
        );
        if ($rs->RecordCount()>0)
        {
            while (!$rs->EOF) {
                $data[]=$rs->fields;
                $rs->MoveNext();
            }

            $tpl_data = array('data'=>$data);
            $tpl = 'order_serials.html';
            $template = new Template();
            $template->getTemplatePath($tpl, 'xt_serials', 'admin', 'plugin');

            ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':tplData')) ? eval($plugin_code) : false;
            $html = $template->getTemplate('xt_serials_order_smarty', $tpl, $tpl_data);
            $html = preg_replace("/(\r\n)+|(\n|\r)+/", "", $html);
        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

        return $html;
	}

	function getSerialsFrontend($orders_id) {
		global $db, $xtPlugin;
		$serials = array();
		$rs = $db->Execute(
			"SELECT op.*, ps.* FROM ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS_SERIAL." ps WHERE op.orders_id=ps.orders_id and op.orders_products_id=ps.orders_products_id and op.orders_id=?",
			array($orders_id)
		);
		if ($rs->RecordCount()>0) {
			while (!$rs->EOF) {
				$serials[]=$rs->fields;
				$rs->MoveNext();
			}
		}

		if (count($serials)==0) return;

		$tpl_data = array('serials'=>$serials);
		$tmp_data = '';
		$tpl = 'history_info.html';
		$template = new Template();
		$template->getTemplatePath($tpl, 'xt_serials', '', 'plugin');

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':tplData')) ? eval($plugin_code) : false;

        $tmp_data = $template->getTemplate('xt_serials_history_smarty', $tpl, $tpl_data);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

		echo $tmp_data;
	}

	function _getParams() {

        global $xtPlugin;

		$params = array();

		$header['serial_id'] = array('type' => 'hidden');
		$header['orders_id'] = array('type' => 'hidden');
		$header['orders_products_id'] = array('type' => 'hidden');
		$header['products_id'] = array('type' => 'hidden');

		$params['header']         = $header;
		$params['display_searchPanel']  = true;
		$params['master_key']     = $this->_master_key;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

		return $params;
	}


	function _get($ID = 0) {
		global $xtPlugin, $db, $language;

		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}
        else $obj = new stdClass();

		if (!$ID && !isset($this->sql_limit)) {
			$this->sql_limit = "0,25";
		}

        $sql_where = '';
        if ($this->url_data['query']) {
            $sql_where = $this->_getSearchIDs($this->url_data['query']);
        }

        if (! empty($sql_where))
            $sql_where .= " AND ";
        $sql_where .= 'products_id=' . ( int ) $this->url_data['products_id'];

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':getData')) ? eval($plugin_code) : false;
        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, $this->sql_limit);


		if ($this->url_data['get_data']){
			$data = $table_data->getData();
		}elseif($ID){
			$data = $table_data->getData($ID);
		}else{
			$data = $table_data->getHeader();
		}

		if($table_data->_total_count!=0 || !$table_data->_total_count)
		$count_data = $table_data->_total_count;
		else
		$count_data = count($data);

		$obj->totalCount = $count_data;
		$obj->data = $data;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

		return $obj;
	}

	function _set($data, $set_type='edit'){
		global $db,$language,$filter, $xtPlugin;

		if($set_type=='new'){
			$data['products_id'] = (int)$this->url_data['products_id'];
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':setdata')) ? eval($plugin_code) : false;

		$obj = new stdClass;
		$o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
		$obj = $o->saveDataSet();

		$obj->success = true;

		return $obj;
	}


	function _unset($id = 0) {
		global $db, $xtPlugin;
		if ($id == 0) return false;
		if ($this->position != 'admin') return false;
		$id=(int)$id;
		if (!is_int($id)) return false;

		$db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", array($id));

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':setdata')) ? eval($plugin_code) : false;
	}

    /*
     * @param string $search_data:	zu suchende Daten
     */
    function _getSearchIDs($search_data) {

        global $filter;

        $sql_data_array = '';

        // field names to search
        $sql_tablecols = array (
            'serial_number',
            'orders_id',
            'orders_products_id'
        );
        foreach ( $sql_tablecols as $tablecol ) {
            $sql_where[] = "(" . $tablecol . " LIKE '%" . $filter->_filter($search_data) . "%')";
        }
        if (is_array($sql_where)) {
            $sql_data_array = " (" . implode(' OR ', $sql_where) . ")";
        }

        return $sql_data_array;
    }
}
