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


class callback extends xt_backend_cls{

	protected $_table = TABLE_CALLBACK_LOG;
	protected $_table_lang = null;
	protected $_table_seo = null;
	protected $_master_key = 'id';
	protected $log_callback_data = true;
	protected $ip = array();
	protected $logFile = 'callback.txt';

	/**
	 * write log to file
	 *
	 * @param string $data
	 * @param string $file
	 */
	function _writeLogFile($data)
	{

		$line = 'CALLBACK|' . date("d.m.Y H:i", time()) . '|';

		foreach ($data as $key => $val)
			$line .= $key . ':' . $val . '|';

		error_log($line . "\n", 3, $this->logFile);
	}

	/**
	 * Add entry to callback log
	 *
	 * available fields:
	 * module
	 * orders_id
	 * transaction_id
	 * callback_data -> serialized array
	 *
	 * @param array $log_data
	 */
	function _addLogEntry($log_data)
	{
		global $db;
		if (is_array($log_data['callback_data'])) $log_data['callback_data'] = serialize($log_data['callback_data']);
		else if(empty($log_data['callback_data'])) $log_data['callback_data'] = '';
		if(empty($log_data['error_msg'])) $log_data['error_msg'] = '';
		if(empty($log_data['error_data'])) $log_data['error_data'] = '';
		if (empty($log_data['transaction_id'])) $log_data['transaction_id']='';
		if (empty($log_data['module'])) $log_data['module']='';
		if (empty($log_data['orders_id'])) $log_data['orders_id']=0;
		if (empty($log_data['created'])) $log_data['created'] =  $db->BindTimeStamp(time());

		$db->AutoExecute(TABLE_CALLBACK_LOG,$log_data,'INSERT');
		$last_id = $db->Insert_ID();
		return $last_id;
	}

	/**
	 * Update order Status, and send status mail
	 *
	 * @param int $new_order_status 
	 */
	function _updateOrderStatus($new_order_status,$send_mail='true',$callback_id='',$callback_message='') {
		$order = new order($this->orders_id,$this->customers_id);
		if ($callback_id==null) $callback_id=''; 
		$order->_updateOrderStatus($new_order_status,'',$send_mail,'true','IPN',$callback_id,$callback_message);
	}

	function _getParams() {
		$params = array();

		$header['orders_id'] = array('readonly' => true, 'width' => 420);
		$header['transaction_id'] = array('readonly' => true, 'width' => 420);
		$header['created'] = array('readonly' => true, 'width' => 420);
		$header['class'] = array('readonly' => true, 'width' => 420);
		$header['error_msg'] = array('readonly' => true, 'type' => 'textarea', 'width' => 800, 'height' => '60px');
		$header['error_data'] = array('readonly' => true, 'type' => 'textarea', 'width' => 800);
		$header['callback_data'] = array('readonly' => true, 'type' => 'textarea', 'width' => 800);
		$header['module'] = array('readonly' => true, 'width' => 420);


		$params['header']         = $header;
		$params['master_key']     = $this->_master_key;
		$params['default_sort']   = $this->_master_key;
        
        $params['SortField'] = $this->_master_key;
        $params['SortDir'] = "DESC";

		$params['display_newBtn'] = false;
		$params['display_deleteBtn'] = false;
		$params['display_editBtn'] = true;
		$params['display_searchPanel']  = false;

		
		if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
			$params['include'] = array ('id','created','orders_id','module', 'transaction_id', 'class');
		}
		
		return $params;
	}

	function _get($ID = 0) {
		global $xtPlugin, $db, $language;

		if ($this->position != 'admin') return false;

		if ($ID === 'new') {
			$obj = $this->_set(array(), 'new');
			$ID = $obj->new_id;
		}

		$ID=(int)$ID;

        $where = '1';
        require(_SRV_WEBROOT."xtFramework/admin/filter/class.callbackPost.php");
        if (isset($where_ar) && count($where_ar) > 0)
        {
            $where .= ' AND '.implode(' AND ', $where_ar);
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,50";
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where, $this->sql_limit);

		
		if ($this->url_data['get_data']){
			$data = $table_data->getData();
            $data_count = $table_data->_total_count;
		}elseif($ID){
			$data = $table_data->getData($ID);

			if (strlen($data[0]['callback_data'])>0) {
				$callback_data = unserialize($data[0]['callback_data']);
				$callback = array();
				if (is_array($callback_data)) {
				foreach ($callback_data as $key=>$val) {
					define('TEXT_DATA_'.strtoupper($key),$key);
					$callback['data_'.$key] = $val;
				}
				
				unset($data[0]['callback_data']);
				$data[0] = array_merge($data[0],$callback);
				}
			}	
			if (strlen($data[0]['error_data'])>0) {
				$callback_data = unserialize($data[0]['data_error']);
				$callback = array();
				if (is_array($callback_data)) {
				foreach ($callback_data as $key=>$val) {
					define('TEXT_ERROR_'.strtoupper($key),$key);
					$callback['error_'.$key] = $val;
				}
				unset($data[0]['data_error']);
				$data[0] = array_merge($data[0],$callback);
				}
			}
			
			$defaultOrder = array(
                'orders_id',
                'module',
                'transaction_id',
                'created',
                'class',
                'callback_data',
                'error_msg',
                'error_data',
            );

            $orderedData = array();
            foreach ($defaultOrder as $key) {
                $orderedData[$key] = $data[0][$key];
            }
            $data = array($orderedData);

		}else{
			$data = $table_data->getHeader();
		}

        $obj = new stdClass;
        if($data_count!=0 || !$data_count)
            $count_data = $data_count;
        else
            $count_data = count($data);

        $obj->totalCount = $count_data;
        $obj->data = $data;

        return $obj;
	}

    function _unset($id = 0)
    {
        global $db;

        if ($this->position != 'admin') return false;

        if ($id == 'all')
        {
            $where = '1';
            require(_SRV_WEBROOT."xtFramework/admin/filter/class.callbackPost.php");
            if (isset($where_ar) && count($where_ar) > 0)
            {
                $where .= ' AND '.implode(' AND ', $where_ar);
            }
            $db->Execute("DELETE FROM ". $this->_table ." WHERE $where");
        }
        else {
            $id=(int)$id;
            if (!is_int($id)) return false;
            $db->Execute("DELETE FROM ". $this->_table ." WHERE ".$this->_master_key." = ?", [$id]);
        }
    }
}