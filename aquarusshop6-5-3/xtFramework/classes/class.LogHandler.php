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


/**
 * System-Log class
 *
 */
class LogHandler extends timer {
	
    protected $_table = TABLE_SYSTEM_LOG;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'log_id';
    protected $popuptriggertime = '30';

    function __construct() {
		$this->_start();
	}

	/**
	 * display parse time for total page generation
	 *
	 * @param boolean $display
	 * @return string parsetime
	 */
	public function parseTime($display = false) {
		$this->_stop();
		if ($display == 'true') {
			return $this->timer_display();
		}
		return false;
	}

	/**
	 * display page parse time
	 *
	 */
	public function timer_display() {
		echo '<div class="parseTime">Parse Time: '.$this->timer_total.'s</div>';
	}
	
	/**
	 * add log entry to log table
	 *
	 * @param string $class error class (eg error, success, warning)
	 * @param string $module module/plugin
	 * @param int $identification int identifier for entry (like customers_id etc)
	 * @param array $log_data log data
	 */
	public function _addLog($class, $module='', $identification=0, $log_data = '') {
		global $db;
		
		if (is_array($log_data)) $log_data = serialize($log_data);
		
		$insert_data = array();
		$insert_data['class']=$class;
		$insert_data['message_source'] = $module;
		$insert_data['identification'] = (int) $identification;
		$insert_data['data'] = $log_data;
		$db->AutoExecute(TABLE_SYSTEM_LOG,$insert_data);
		
		return $db->Insert_ID();
	}

    /**
     * add log entry to log table with popup trigger
     *
     * @param $class
     * @param string $module
     * @param int $identification
     * @param $log_data array of message msg [ de => x, en => x ]0
     * @param int $popup_trigger
     * @return mixed
     */
	public function _addPopupNotification($class,$module='',$identification=0,$log_data) {
        global $db;

        if (is_array($log_data)) $log_data = json_encode($log_data);

        $insert_data = array();
        $insert_data['class']=$class;
        $insert_data['message_source'] = $module;
        $insert_data['identification'] = (int) $identification;
        $insert_data['data'] = $log_data;
        $insert_data['popuptrigger'] = 1;
        $db->AutoExecute(TABLE_SYSTEM_LOG,$insert_data);

        return $db->Insert_ID();
    }


    /**
     * query latest popup entry to display
     * @return stdClass
     */
    public function getLastPopupNotification() {
        global $db,$language;

        $query = "SELECT * FROM " . TABLE_SYSTEM_LOG . " WHERE popuptrigger='1' and DATE_ADD(created, INTERVAL ".$this->popuptriggertime." second) >= NOW()  ORDER BY log_id DESC LIMIT 1";

        $rs = $db->Execute($query);

        if ($rs->RecordCount() == 1) {

            $msg = $rs->fields['data'];
            $msg = json_decode($msg);
            if (is_object($msg)) {

                $obj = new stdClass();
                $obj->success = false;

                if (property_exists($msg,'msg')) {
                    if (property_exists($msg->msg,$language->default_language)) {
                        $obj->message = $msg->msg->{$language->default_language};
                        return $obj;
                    }
                }
            }
        }

    }
	
	/**
	 * Get log messages for given module
	 * @param string $module_id
	 * @param string $identification
	 * @return array
	 */
	public function getLogMessages($module_id, $identification, $conditions = '', $limit = '', $orderBy = 'ORDER BY created DESC') {
		global $db;
		$messages = array();
		
		$query = "SELECT * FROM " . TABLE_SYSTEM_LOG . " WHERE message_source='" . $module_id . "' and identification=?";
		if (!empty($conditions)) {
			$query .= " {$conditions} ";
		}
		
		$query .= " " . $orderBy . " ";
		if (!empty($limit)) {
			$query .= $limit;
		}
		$rs = $db->Execute($query, array((int)$identification));
		
		if ($rs->RecordCount() > 0) {
			while (!$rs->EOF) {
				$messages[] = $rs->fields;
				$rs->MoveNext();
			}
			$rs->Close();
		}
		
		return $messages;
	}
	
	/**
	 * Clear messages for given module
	 * @param string $module_id
	 * @param string $identification
	 */
	public function clearLogMessages($module_id, $identification) {
		global $db;
		$db->Execute(
            "DELETE FROM " . TABLE_SYSTEM_LOG . " WHERE message_source=? and identification=?",
            array($module_id, (int)$identification)
        );
	}
	
	/**
	 * Outputs log message
	 * @param string $module_id
	 * @param string $identification
	 */
	public function showLog($module_id, $identification, $conditions = '', $limit = '', $orderBy = 'ORDER BY created DESC') {
		$messages = $this->getLogMessages($module_id, $identification, $conditions , $limit , $orderBy);
		 
		if (!empty($messages)) {
			echo '<ul class="stack">';
			foreach ($messages as $message) {
				$data = unserialize($message['data']);
				if ($data['message'] != '') {
					$m = $data['message'];
				}
				if ($data['time'] != '') {
					$m .= ' ' . date("H:i:s", $data['time']);
				}
				 
				echo '<li class="' . $message['class'] . '">' . $m . '</li>';
			}
			echo '</ul>';
		}
	}

	/**
	 * send error report mail to administrator
	 * 
	 * using php mail() function
	 *
	 * @param string $line email content
	 * @param string $subject email subject
	 */
	public function sendDebugMail($line,$subject){
        
        $headers = 'From: '._CORE_DEBUG_MAIL_ADDRESS. "\r\n" .
					'Reply-To: '._CORE_DEBUG_MAIL_ADDRESS . "\r\n" .
    				'X-Mailer: PHP/' . phpversion();
        
		mail(_CORE_DEBUG_MAIL_ADDRESS,$subject,$line,$headers);
	}
    
    public function setPosition ($position) {
        $this->position = $position;
    }
    
    public function _getParams() {
        $params = array();

        $header['identification'] = array('disabled' => 'true','width'=>'400');
        $header['created'] = array('disabled' => 'true');
        $header['class'] = array('disabled' => 'true');
        $header['message_source'] = array('disabled' => 'true','width'=>'400');

        $params['header']         = $header;
        $params['master_key']     = $this->_master_key;
        $params['default_sort']   = $this->_master_key;

        $params['display_newBtn'] = false;
        $params['display_deleteBtn'] = true;
        $params['display_editBtn'] = true;
        
        $params['SortField'] = 'created';
        $params['SortDir'] = "DESC";
        
        if($this->url_data['pg']=='overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true){
            $params['include'] = array ('log_id','created','message_source', 'identification', 'class');
        }
        
        return $params;
    }

    public function _get($ID = 0) {
        global $xtPlugin, $db, $language;

        if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }
        
        $ID=(int)$ID;

        $where = '1';
        require(_SRV_WEBROOT."xtFramework/admin/filter/class.LogHandlerPost.php");
        if (isset($where_ar) && count($where_ar) > 0)
        {
            $where .= ' AND '.implode(' AND ', $where_ar);
        }

        if (!$ID && !isset($this->sql_limit)) {
            $this->sql_limit = "0,25";
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $where, $this->sql_limit);

        
        if ($this->url_data['get_data']){
            $data = $table_data->getData();

            $data_count = $table_data->_total_count;
        }elseif($ID){
            $data = $table_data->getData($ID);

            if (strlen($data[0]['data'])>0) {
                $callback_data = unserialize($data[0]['data']);
                $callback = array();
                if (is_array($callback_data)) {
                foreach ($callback_data as $key=>$val) {
                    define('TEXT_DATA_'.strtoupper($key),$key);
                    $callback['data_'.$key] = $val;
                }
                
                unset($data[0]['data']);
                $data[0] = array_merge($data[0],$callback);
                }
            }    
            
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
    
    public function _set($ID=0) {
        $obj = new stdClass;
        $obj->success = true;
        return $obj;
    }

    public function Log2File($file,$dir,$data) {

        ob_start();
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        $log = ob_get_contents();
        ob_end_clean();
		
        $error_file = _SRV_WEBROOT.$dir.$file;
		
        $fp = fopen($error_file,"a+");
        fputs($fp, $log);
        fclose($fp);
		
        if (is_array($data)) {

		    $time = @date('[d/M/Y:H:i:s]');
		
		    $data = implode('|',$data);
		
		    $log = $time.'| '.$data.'|'.PHP_EOL;
		
		    $error_file = _SRV_WEBROOT.$dir.$file;
		
		    $fp = fopen($error_file,"a+");
		    fputs($fp, $log);
		    fclose($fp);
    	}
    }

    function _unset($id = 0)
    {
        global $db;

        if ($this->position != 'admin') return false;

        if ($id == 'all')
        {
            $where = '1';
            require(_SRV_WEBROOT."xtFramework/admin/filter/class.LogHandlerPost.php");
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