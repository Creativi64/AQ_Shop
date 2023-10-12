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


class skrill_transactions
{

    protected $_table = TABLE_CALLBACK_LOG;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'id';
    protected $log_callback_data = true;
    protected $ip = array();
    protected $logFile = 'skrill_callback.txt';

    /**
     * write log to file
     *
     * @param string $data
     * @param string $file
     */
    function _writeLogFile ($data)
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
    function _addLogEntry ($log_data)
    {
        global $db;
        if (is_array($log_data['callback_data'])) $log_data['callback_data'] = serialize($log_data['callback_data']);
        //$log_data['created'] =  $db->BindTimeStamp(time());
        if ($log_data['transaction_id'] == null) $log_data['transaction_id'] = '';
        $db->AutoExecute(TABLE_CALLBACK_LOG, $log_data, 'INSERT');
        $last_id = $db->Insert_ID();
        return $last_id;
    }

    /**
     * Update order Status, and send status mail
     *
     * @param int $new_order_status
     */
    function _updateOrderStatus ($new_order_status, $send_mail = 'true', $callback_id = '')
    {
        $order = new order($this->orders_id, $this->customers_id);
        if ($callback_id == null) $callback_id = '';
        $order->_updateOrderStatus($new_order_status, '', $send_mail, 'true', 'IPN', $callback_id);
    }


    function setPosition ($position)
    {
        $this->position = $position;
    }

    function _getParams ()
    {
        global $language;
        $params = array();

        //$header['orders_id'] = array('disabled' => 'true');
        //$header['transaction_id'] = array('readonly' => 'true');
        //$header['mb_transaction_id'] = array('readonly' => 'true');
        //$header['payment'] = array('disabled' => 'true');
        //$header['created'] = array('disabled' => 'true');
        $header['callback_data'] = array('type' => 'textarea');
        $header['class'] = array('type' => 'hidden', 'disabled' => 'true');
        $header['error_msg'] = array('type' => 'hidden', 'disabled' => 'true');
        $header['module'] = array('type' => 'hidden', 'disabled' => 'true');

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['default_sort'] = $this->_master_key;

        $params['SortField'] = $this->_master_key;
        $params['SortDir'] = "DESC";
        $params['display_newBtn'] = false;
        $params['display_deleteBtn'] = false;

        if ($this->url_data['edit_id'])
        {
            $params['display_editBtn'] = false;
            //$params['include'] = array('orders_id', 'payment', 'transaction_id','created');
        } else {
            $params['display_editBtn'] = true;
            $params['exclude'] = array('module', 'error_msg', 'class', 'error_data', 'callback_data');

            $rowActions[] = array('iconCls' => 'xt_skrill_refunds', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_XT_SKRILL_PLUS_REFUNDS);
            $js = "var edit_id = record.id;";
            $js .= "addTab('adminHandler.php?plugin=xt_skrill&load_section=skrill_refunds&pg=overview&new=true&callback_log_id='+edit_id,'" . TEXT_XT_SKRILL_PLUS_REFUNDS . "')";

            $rowActionsFunctions['xt_skrill_refunds'] = $js;
            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }

        return $params;
    }


    function getPaymentDetails($callback_log_id){
        global $db;
        $obj = new stdClass;
        if ($callback_log_id>0){
            $rc= $db->Execute("SELECT * FROM ".TABLE_CALLBACK_LOG." WHERE id = ".$callback_log_id);
            if ($rc->RecordCount()>0){
                $obj->order_id = $rc->fields['orders_id'];
                $result = $db->Execute("SELECT customers_id,PPP_PAYMENTID FROM ".TABLE_ORDERS." WHERE orders_id = ? ",array($obj->order_id));
                if ($result->RecordCount()>0){
                    $obj->customers_id = $result->fields['customers_id'];
                }
            }
        }
        return $obj;

    }

    function _get ($ID = 0)
    {
        if($this->url_data['pg']=='getPaymentDetails'){
            $this->getPaymentDetails();
            die();
        }
        global $xtPlugin, $db, $language;

        if ($this->position != 'admin') return false;

        if ($ID === 'new') {
            $obj = $this->_set(array(), 'new');
            $ID = $obj->new_id;
        }

        $ID = (int)$ID;

        $sql_where = ' module = "xt_skrill" AND orders_id !=0 AND transaction_id != "" AND class = "callback_data"';

        $where_ar = array();
        if (count($where_ar) > 0) {
            $where1 = " AND " . implode(" AND ", $where_ar);
            $sql_where = $sql_where . $where1;
        }

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, $sql_where, '', '', '', ' ORDER BY orders_id DESC');

        if ($this->url_data['get_data'])
        {
            $sql = "SELECT * from
              (select * FROM ".$this->_table." where  module = 'xt_skrill' AND orders_id !=0 AND transaction_id != '' AND class = 'callback_data' GROUP BY transaction_id ORDER BY created) x
               ORDER BY orders_id DESC";
            $data = $db->GetArray($sql);

            foreach($data as $k=>$d)
            {
                try
                {
                    $ar = unserialize($d['callback_data']);
                    if (is_array($ar))
                    {
                        $data[$k]['mb_transaction_id'] = $ar['mb_transaction_id'];
                    }
                }
                catch(Exception $e){}
            }

        } elseif ($ID)
        {
            $table_data->_filterData = false;
            $data = $table_data->getData($ID);
            try
            {
                $ar = unserialize($data[0]['callback_data']);
                if (is_array($ar))
                {
                    $data[0]['mb_transaction_id'] = $ar['mb_transaction_id'];
                    $data[0]['callback_data'] = json_encode(print_r($ar,true));
                }
            }
            catch(Exception $e){}

        } else {
            $data = $table_data->getHeader();
            $data[0]["mb_transaction_id"] = "";
        }
        $obj = new stdClass;
        $obj->totalCount = count($data);
        $obj->data = $data;

        return $obj;
    }

    function _set ($ID = 0)
    {

        $obj = new stdClass;
        $obj->success = true;
        return $obj;

    }

}
