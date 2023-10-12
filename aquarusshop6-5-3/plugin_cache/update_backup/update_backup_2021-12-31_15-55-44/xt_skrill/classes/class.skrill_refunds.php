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


class skrill_refunds
{

    protected $_table = TABLE_SKRILL_REFUNDS;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'refunds_id';
    protected $log_callback_data = true;

    function __construct (){}

    function setPosition ($position){
        $this->position = $position;
    }

    function _getParams ()
    {
        $params = array();
        $header = array();

        $header['orders_id'] = array();
        $header['transaction_id'] = array();
        $header['callback_log_id'] = array('readonly' => 'true');
        $header['error_data'] = array('disabled' => 'true');
        $header['error_msg'] = array('disabled' => 'true');
        $header['callback_data'] = array('disabled' => 'true');
        $header['sum_refunded'] = array('readonly' => 'true');
        $header['ORDERS_TOTAL_AMOUNT'] = array('readonly' => 'true');

        $header['success'] = array('readonly' => true, 'type' => 'status');
        $header['refunds_type'] = array(
            'type' => 'dropdown',
            'url' => 'DropdownData.php?get=skrill_refunds_type&plugin_code=xt_skrill');

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['default_sort'] = $this->_master_key;
        $params['GroupField']     = "transaction_id";

        $params['display_newBtn'] = false;
        $params['display_deleteBtn'] = false;
        if ($this->url_data['edit_id'] || $this->url_data['new'] == true) {
            $params['display_editBtn'] = false;
        } else {
            $params['display_editBtn'] = true;
        }


        if ($this->url_data['pg'] == 'overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true) {
            $params['include'] = array('orders_id_hidden','orders_id', 'transaction_id','refund_total', 'success', 'refunds_type');
            $params['exclude'] = array('refunded');
        } else {
            $params['exclude'] = array('date_added', 'last_modified', 'refunded', 'callback_log_id', 'callback_data', 'error_data', 'error_msg');
        }

        $refunded = true;
        if($this->url_data['edit_id'])
        {
            $refunded = $this->_isFullyRefunded($this->url_data['edit_id']);
        }
        if (!$refunded || $this->url_data['new'] == true)
        {
            $button_js = "
                Ext.getCmp('skrill_refunds" . $this->_AdminHandler->getSelectionItem() . "-grideditform').getForm().submit({ url: 'adminHandler.php?load_section=skrill_refunds&plugin=xt_skrill&parentNode=node_skrill_refunds&sec=" . _SYSTEM_SECURITY_KEY . "&gridHandle=skrill_refundsgridForm&save=true',

                success: function(form,action){

                            if ((typeof(action.result) != 'undefined') && (typeof(action.result.message) != 'undefined') && (action.result.message.length > 0))
                            {
                                 var msg =  action.result.message.replace(/\"/g , '');
                                 Ext.Msg.alert('Success', msg);
                            }
                            else {
                                Ext.Msg.alert('Success', 'Success');
                            }
                            contentTabs.remove(contentTabs.getActiveTab());
                            if(typeof(Ext.getCmp('skrill_refundsgridForm')) != 'undefined') Ext.getCmp('skrill_refundsgridForm').getStore().reload();
                            if(typeof(Ext.getCmp('skrill_transactionsgridForm')) != 'undefined') Ext.getCmp('skrill_transactionsgridForm').getStore().reload();

                            },
                failure: function(form,action){

                            if ((typeof(action.result) != 'undefined') && (typeof(action.result.error_message) != 'undefined') && (action.result.error_message.length > 0))
                            {
                                 var msg =  action.result.error_message.replace(/\"/g , '');
                                 Ext.Msg.alert('Error', msg);
                            }
                            else {
                                Ext.Msg.alert('Error', 'Error');
                            }
                            contentTabs.remove(contentTabs.getActiveTab());
                            if(typeof(Ext.getCmp('skrill_refundsgridForm')) != 'undefined') Ext.getCmp('skrill_refundsgridForm').getStore().reload();
                            if(typeof(Ext.getCmp('skrill_transactionsgridForm')) != 'undefined') Ext.getCmp('skrill_transactionsgridForm').getStore().reload();
                }

                            , waitMsg: 'Laden' })

";

            $js = "
        function doRefund()
        {
            $button_js

        }
        ";
            $rowActions[] = array('iconCls' => 'xt_skrill_refunds_do', 'qtipIndex' => 'qtip1', 'tooltip' => TEXT_XT_SKRILL_PLUS_REFUNDS_DO);
            $js .= "doRefund()";
            $rowActionsFunctions['xt_skrill_refunds_do'] = $js;

            $params['rowActions'] = $rowActions;
            $params['rowActionsFunctions'] = $rowActionsFunctions;
        }

        return $params;
    }



    function _get ($ID = 0)
    {
        global $xtPlugin, $db, $language;

        if ($this->position != 'admin') return false;

        $ID = (int)$ID;

        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key, '', '', '', '', ' ORDER BY orders_id DESC');

        if ($this->url_data['get_data']) {
            $data = $table_data->getData();
        } elseif ($_GET['new'] == true)
        {
            if (isset($_GET['callback_log_id'])) {
                $data[] = $this->_getData($_GET['callback_log_id']);
                $sum = $db->GetOne("SELECT SUM(refund_total) FROM ".$this->_table." WHERE refunded = 1 AND success = 1 AND orders_id = ?",array((int)$data[0]['orders_id']));
                $order = new order((int)$data[0]['orders_id'], -1);
                $data[0]['ORDERS_TOTAL_AMOUNT'] = $order->order_total['total']['plain'];
                $data[0]['sum_refunded'] = $sum;
                $data[0]['refund_total'] = $data[0]['ORDERS_TOTAL_AMOUNT'] > $sum ? $data[0]['ORDERS_TOTAL_AMOUNT'] - $sum : 0;
            }
        }
        elseif ($ID) {
            $data = $table_data->getData($ID);

        }
        else {
            $data = $table_data->getHeader();

        }

        $obj = new stdClass;
        $obj->totalCount = count($data);
        $obj->data = $data;

        return $obj;
    }

    function _set ($data, $set_type = 'edit')
    {
        //error_log(print_r($data,true));
        global $db, $language, $filter, $seo, $currency;

        $obj = new stdClass;
        //error_log('1   '.$data['refunds_id']);
        if (!isset($data['refunds_id'])) {
            $set_type = 'new';
            $data['date_added'] = $db->BindTimeStamp(time());
            $data['last_modified'] = $db->BindTimeStamp(time());
        }
        else{
            $o_data = $this->_get($data['refunds_id']);
            $db_data = $o_data->data[0];
            $data['transaction_id'] =  $db_data['transaction_id'];
            $data['refund_data'] =  $db_data['refund_data'];
            $data['orders_id'] =  $db_data['orders_id'];
            $data['refunded'] =  $db_data['refunded'];
            $data['success'] =  $db_data['success'];
        }

        //check entered data (refund_type vs total)
        $send_refund = false;
        $isFullyRefunded = $this->_isFullyRefunded($data['refunds_id']);
        //error_log('2   '.$isFullyRefunded);
        //error_log('21   '.$data['success']);
        if ($isFullyRefunded == false)
        {
            $o_id = $data['orders_id'] ? $data['orders_id'] : $this->getOrders_id($data['refunds_id']);
            $o_total = $this->getTotal($o_id);

            switch ($data['refunds_type']) {
                case 'Full':
                    if ($o_total != $data['refund_total'] || $data['refund_total'] == 0 || $o_total < $data['refund_total']) {
                        //$obj->failed = false;
                        //return $obj;
                    }
                    break;

                case 'Partial' :
                    if ($data['refund_total'] >= $o_total || $data['refund_total'] <= 0) {
                        $obj->failed = false;
                        $obj->error_message = "partial refunds value to high or 0";
                        return $obj;
                    }
                    break;
            }

            if ($data['success'] == 0) {
                $send_refund = true;
            }

        } else {
            $send_refund = false;
        }


        $oC = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $objC = $oC->saveDataSet();

        if ($set_type == 'new') { // edit existing
            $obj->new_id = $objC->new_id;
            $obj->success = true;
            $data = array_merge($data, array($this->_master_key => $objC->new_id));
        }
        //error_log('3   '.$objC->success);
        //error_log('4   '.$send_refund);
        if ($objC->success) {
            //send refund to skrill
            if ($send_refund == true && !$isFullyRefunded) {
                $callback_log_id = $this->getCallback_log_id($data['refunds_id']);
                $data_r = $this->_getData($callback_log_id);
                $data_r['refund_memo'] = $data['refund_memo'];
                $data_r['refunds_type'] = $this->getRefundtype($data['refunds_id']);
                $data_r['refund_total'] = round($this->getRefundTotal($data['refunds_id']), $currency->decimals);

                // TODO send it
                include_once _SRV_WEBROOT.'plugins/xt_skrill/classes/class.xt_skrill.php';
                try
                {
                    $skrill = new xt_skrill();
                    $refund_amount = $data['refunds_type'] == 'Full' ? false : $data['refund_total'];
                    $refund_note = !empty($data_r['refund_memo']) ? $data_r['refund_memo'] : false;
                    $refund = $skrill->refund($data['transaction_id'], $data['refunds_id'], $refund_amount, $refund_note);

                    $callback_data = serialize((array)$refund);

                    $log_data['orders_id'] = $data['orders_id'];
                    $log_data['callback_data'] = $callback_data;
                    $log_data['class'] = "success_refund";
                    $log_data['transaction_id'] = $data['transaction_id'];
                    $log_id = $skrill->_addCallbackLog($log_data, $data['transaction_id']);

                    // test
                    //$refund['status'] = 0;

                    if($refund['status'] == 2)
                    {
                        $db->Execute("UPDATE " . $this->_table . ' SET refunded = 1, success = 1, callback_log_id=?, callback_data=? WHERE refunds_id=?', array($log_id, $callback_data, (int)$data['refunds_id']));

                        $o_refunds = new order($data_r['orders_id'], -1);
                        $send_email = false;
                        $status = XT_SKRILL_REFUNDED;
                        $comments = 'Refund_id:' . $data['refunds_id'] . ', Memo:' . $data_r['refund_memo'];
                        $o_refunds->_updateOrderStatus($status, $comments, $send_email);
                        $obj->success = true;
                        $obj->message = TEXT_SKRILL_REFUNDED;
                    }
                    else if ($refund['status'] == 0)
                    {
                        $db->Execute("UPDATE " . $this->_table . ' SET refunded = 0, success = 0, callback_log_id=?, callback_data=? WHERE refunds_id=?', array($log_id, $callback_data, (int)$data['refunds_id']));

                        throw new Exception( 'REFUND_PENDING');
                    }
                }
                catch(Exception $e)
                {
                    $obj->success = false;
                    $obj->failed = true;
                    $known_errors = array('BALANCE_NOT_ENOUGH','CC_REFUND_FAILED','RESERVE_EXCEEDED','GENERIC_ERROR', 'REFUND_PENDING');
                    $msg = $e->getMessage();
                    if(in_array($msg, $known_errors))
                    {
                        $msg = constant('TEXT_SKRILL_ERROR_'.$msg);
                    }
                    $obj->error_message = $msg;
                    return $obj;
                }
            }
            else if ($isFullyRefunded) {
                $obj->success = false;
                $obj->failed = true;
                $obj->error_message = TEXT_SKRILL_REFUND_ALREADY_EXECUTED;
                return $obj;
            }


        } else {
            $obj->failed = true;
        }

        //error_log(print_r($obj,true));
        return $obj;
    }

    function getCurrency ($orders_id)
    {
        global $currency;

        $refund_order = new order($orders_id, -1);
        return $refund_order->order_data[''];

    }

    public function getRefundtype ($refunds_id)
    {
        global $db;
        $record = $db->Execute("SELECT * FROM " . $this->_table . " WHERE refunds_id = ? ",array((int)$refunds_id));
        if ($record->RecordCount() > 0) {
            return $record->fields['refunds_type'];
        }
        return 'Full';
    }

    public function getRefundTotal ($refunds_id)
    {
        global $db;
        $record = $db->Execute("SELECT * FROM " . $this->_table . " WHERE refunds_id = ?",array((int)$refunds_id ));
        if ($record->RecordCount() > 0) {
            return $record->fields['refund_total'];
        }
        return 0;
    }

    public function getTotal ($oID)
    {
        $o_refunds = new order($oID, -1);
        $o_data = $o_refunds->_getOrderTotalData($oID);

        return $o_refunds->order_total['total']['plain'];
    }

    public function getCurrencyCode ($oID)
    {
        $o_refunds = new order($oID, -1);
        $o_data = $o_refunds->_getOrderData($oID);
        return $o_data['currency_code'];
    }

    public function getOrders_id ($refunds_id)
    {
        global $db;
        $record = $db->Execute("SELECT orders_id FROM " . $this->_table . " WHERE refunds_id = ? ",array((int)$refunds_id));
        if ($record->RecordCount() > 0) {
            return $record->fields['orders_id'];
        }
        return 0;
    }

    public function getCallback_log_id ($id)
    {
        global $db;
        $record = $db->Execute("SELECT callback_log_id FROM " . $this->_table . " WHERE refunds_id =? ",array((int)$id));
        if ($record->RecordCount() > 0) {
            return $record->fields['callback_log_id'];
        }
        return 0;
    }

    function _getData ($callback_log_id)
    {
        global $xtPlugin, $db, $language;
        $new_refunds = array();
        $record = $db->Execute("SELECT * FROM " . TABLE_CALLBACK_LOG . " WHERE id = ?", array((int)$callback_log_id));
        if ($record->RecordCount() > 0) {
            $new_refunds['callback_log_id'] = (int)$callback_log_id;

            $new_refunds['orders_id'] = $record->fields['orders_id'];
            $new_refunds['transaction_id'] = $record->fields['transaction_id'];
            $new_refunds['refunded'] = 0;
            $new_refunds['refund_memo'] = 'retoure';
            $new_refunds['refunds_type'] = 'Full';
            $new_refunds['refund_total'] = $this->getTotal($new_refunds['orders_id']);
        }

        return $new_refunds;
    }

    public function _getRefunds ($callback_log_id)
    {
        global $db;
        $record = $db->Execute("SELECT refunds_id FROM " . $this->_table . " WHERE callback_log_id = ? LIMIT 1",array((int)$callback_log_id));
        if ($record->RecordCount() > 0) {
            return $record->fields['refunds_id'];
        }

        return 0;
    }

    public function _isFullyRefunded ($refunds_id)
    {
        global $db;
        $r = $db->GetOne("SELECT 1 FROM " . $this->_table . " WHERE refunded = 1 AND success = 1 AND refunds_id = ?",array((int)$refunds_id));

        return $r == 1 ? true:false;
    }
}
