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


class paypal_plus_refunds
{

    protected $_table = TABLE_PAYPAL_PLUS_REFUNDS;
    protected $_table_lang = null;
    protected $_table_seo = null;
    protected $_master_key = 'ppp_refunds_id';
    protected $log_callback_data = true;

    function __construct (){}

    function setPosition ($position){
        $this->position = $position;
    }

    function _getParams ()
    {
        $params = array();
        $header = array();
        $header['orders_id'] = array('disabled' => 'true');
        $header['transaction_id'] = array('disabled' => 'true');
        $header['callback_log_id'] = array('disabled' => 'true');
        $header['error_data'] = array('disabled' => 'true');
        $header['error_msg'] = array('disabled' => 'true');
        $header['callback_data'] = array('disabled' => 'true');
        //$header['ppp_refunded'] = array('disabled' => 'true');
        $header['success'] = array('readonly' => true, 'type' => 'status');
        $header['ppp_refunds_type'] = array(
            'type' => 'dropdown',
            'url' => 'DropdownData.php?get=ppp_refunds_type&plugin_code=xt_paypal_plus');

        $params['header'] = $header;
        $params['master_key'] = $this->_master_key;
        $params['default_sort'] = $this->_master_key;
        $params['GroupField']     = "transaction_id";

        $params['display_newBtn'] = false;
        $params['display_deleteBtn'] = false;
        $params['display_editBtn'] = true;

        if ($this->url_data['pg'] == 'overview' && !$this->url_data['edit_id'] && $this->url_data['new'] != true) {
            $params['include'] = array('orders_id', 'transaction_id','ppp_payment',  'status', 'total', 'success', 'ppp_refunds_type');
            $params['exclude'] = array('ppp_refunded');
        } else {
            $params['exclude'] = array('date_added', 'last_modified', 'ppp_refunded', 'callback_log_id', 'callback_data', 'error_data', 'error_msg', 'ppp_refunded');
        }

        return $params;
    }



    function _get ($ID = 0)
    {
        global $xtPlugin, $db, $language;

        if ($this->position != 'admin') return false;

        if ($_GET['new'] == true) {
            $new_refunds = array();
            if (isset($_GET['callback_log_id'])) {
                $new_refunds = $this->_getData($_GET['callback_log_id']);
            }

            $ID = $this->_getRefunds($_GET['callback_log_id']);
            if ($ID == 0) {
                $obj = $this->_set($new_refunds, 'new');
                $ID = $obj->new_id;
            }
        }

        $ID = (int)$ID;


        $table_data = new adminDB_DataRead($this->_table, $this->_table_lang, $this->_table_seo, $this->_master_key);


        if ($this->url_data['get_data']) {
            $data = $table_data->getData();
        } elseif ($ID) {
            $data = $table_data->getData($ID);

        } else {
            $data = $table_data->getHeader();
			
        }

        $obj = new stdClass;
        $obj->totalCount = count($data);
        $obj->data = $data;

        return $obj;
    }

    function _set ($data, $set_type = 'edit')
    {
        global $db, $language, $filter, $seo, $currency;

        $obj = new stdClass;

        if ($set_type == 'new') {
            $data['date_added'] = $db->BindTimeStamp(time());
            $data['last_modified'] = $db->BindTimeStamp(time());
        }

        $data['total'] = round(str_replace(',','.',$data['total']),2);

        //check entered data (refund_type vs total)
        $send_refund = false;
        $isRefunded = $this->_isRefunded($data['ppp_refunds_id']);
        if ($isRefunded == false) {
            if ($set_type == 'edit' && $data['status'] == 1) {
                $o_id = $this->getOrders_id($_GET['edit_id']);
                $o_total = $this->getTotal($o_id);

                switch ($data['ppp_refunds_type']) {
                    case 'Full':
                        if ($o_total != $data['total'] || $data['total'] == 0 || $o_total < $data['total']) {
                            $obj->failed = false;
                            $obj->error_message = __define('PPP_INVALID_REFUND_AMOUNT').' '.$o_total.'#'.$data['total'];
                            return $obj;
                        }
                        break;

                    case 'Partial' :
                        if ($data['total'] >= $o_total || $data['total'] <= 0) {
                            $obj->failed = false;
                            $obj->error_message = __define('PPP_INVALID_REFUND_AMOUNT').' '.$o_total.'#'.$data['total'];
                            return $obj;
                        }
                        break;
                }

                if ($data['success'] == 0) {
                    $send_refund = true;
                }
            }
        } else {
            $send_refund = false;
            if ($data['status'] == 0) {
                $obj->failed = false;
                return $obj;
            }
        }

        $oC = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        $objC = $oC->saveDataSet();

        if ($set_type == 'new') { // edit existing
            $obj->new_id = $objC->new_id;
            $data = array_merge($data, array($this->_master_key => $objC->new_id));
        }

        $refund_data=array();

        if ($objC->success) {
            //send refund to paypal
            if ($send_refund == true) {
                $callback_log_id = $this->getCallback_log_id($data['ppp_refunds_id']);
                $data_r = $this->_getData($callback_log_id);
                $data_r['ppp_refund_memo'] = $data['ppp_refund_memo'];
                $data_r['ppp_refunds_type'] = $this->getRefundtype($data['ppp_refunds_id']);
                $data_r['total'] = round($this->getRefundTotal($data['ppp_refunds_id']), $currency->decimals);
                include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
				$ppp = new paypal_plus;
				$ppp->customer = 'admin_'.time();//$customer_id;
				$ppp->orders_id= $data_r['orders_id'];
				$ppp->position='admin';
				$ppp->generateSecurityToken();
				$rc = $db->Execute("SELECT  PPP_SALEID,PPP_PAYMENTID FROM ".TABLE_ORDERS."  WHERE orders_id = ?",array($data_r['orders_id']));
				if ($rc->RecordCount()>0){
                    try {
                        $sale_details = $ppp->getSaleDetails($rc->fields['PPP_SALEID'],$rc->fields['PPP_PAYMENTID'] );
                    }
                    catch(Exception $e)
                    {
                        $obj->error_message = "Exeption: ". $e->getMessage();
                        $obj->failed = true;
                    }
					$payment_id = $rc->fields['PPP_PAYMENTID'];
					$refun_url='';
					if ($sale_details->links){
						foreach($sale_details->links as $links){
							if ($links->rel=='refund'){
								$refun_url = $links->href;
							}
						}
					}
					
					if ($sale_details->state!='refunded'){
						$refund_data=array("amount"=>array(
													"total"=>number_format($data_r['total'], 2, '.', ''),
													"currency"=>$this->getCurrencyCode($data_r['orders_id'])));
						$refund = $ppp->refundPayment($refund_data,$refun_url,$rc->fields['PPP_PAYMENTID']);
						
						
					}else{
						$refund = new stdClass;
						$refund->message = XT_PAYPAL_PLUS_TRANSACTION_IS_ALREDY_REFUNDED;
					}
					$ppp->unsetPayPalPlusSessions($ppp->customer);
				}
				
				if ($refund->id){
					$db->Execute("UPDATE " . $this->_table . ' SET ppp_refunded = 1, success = 1 WHERE ppp_refunds_id=?', array((int)$data['ppp_refunds_id']));
					$log_data['callback_data'] = serialize((array)$refund);
					$log_data['class'] = "success_refund";
					$log_data['transaction_id'] =$payment_id;
                    $log_data['error_data'] = $refund_data;
					$ppp->_addCallbackLog($log_data,$payment_id);
					$o_refunds = new order($data_r['orders_id'], -1);
					$send_email = false;
					$status = XT_PAYPAL_PLUS_ORDER_STATUS_REFUNDED;
					$comments = 'Refund_id:' . $data['ppp_refunds_id'] . ', Memo:' . $data_r['ppp_refund_memo'];
					$o_refunds->_updateOrderStatus($status, $comments, $send_email);
					$obj->message = TEXT_PPP_ORDER_REFUNDED;
					$obj->success = true;
				}else{
					
					$obj->error_message=$refund->message;
					$obj->failed = true;
				}
            }
        } else {
            $obj->failed = true;
        }

        return $obj;
    }

    function getCurrency ($orders_id)
    {
        global $currency;

        $refund_order = new order($orders_id, -1);
        return $refund_order->order_data[''];

    }

    public function getRefundtype ($ppp_refunds_id)
    {
        global $db;
        $record = $db->Execute("SELECT * FROM " . $this->_table . " WHERE ppp_refunds_id = ? ",array((int)$ppp_refunds_id));
        if ($record->RecordCount() > 0) {
            return $record->fields['ppp_refunds_type'];
        }
        return 'Full';
    }

    public function getRefundTotal ($ppp_refunds_id)
    {
        global $db;
        $record = $db->Execute("SELECT * FROM " . $this->_table . " WHERE ppp_refunds_id = ?",array((int)$ppp_refunds_id ));
        if ($record->RecordCount() > 0) {
            return $record->fields['total'];
        }
        return 0;
    }

    public function getTotal ($oID)
    {
        $o_refunds = new order($oID, -1);
        $o_data = $o_refunds->_getOrderTotalData($oID);

        return round($o_refunds->order_total['total']['plain'],2);
    }

    public function getCurrencyCode ($oID)
    {
        $o_refunds = new order($oID, -1);
        $o_data = $o_refunds->_getOrderData($oID);
        return $o_data['currency_code'];
    }

    public function getOrders_id ($ppp_refunds_id)
    {
        global $db;
        $record = $db->Execute("SELECT orders_id FROM " . $this->_table . " WHERE ppp_refunds_id = ? ",array((int)$ppp_refunds_id));
        if ($record->RecordCount() > 0) {
            return $record->fields['orders_id'];
        }
        return 0;
    }

    public function getCallback_log_id ($id)
    {
        global $db;
        $record = $db->Execute("SELECT * FROM " . $this->_table . " WHERE ppp_refunds_id =? ",array((int)$id));
        if ($record->RecordCount() > 0) {
            return $record->fields['callback_log_id'];
        }
        return 0;
    }

    function _getData ($callback_log_id)
    {
        global $xtPlugin, $db, $language;
        $new_refunds = array();
        $record = $db->Execute("SELECT orders_id,transaction_id FROM " . TABLE_CALLBACK_LOG . " WHERE id = ?", array((int)$callback_log_id));
        if ($record->RecordCount() > 0) {
            $new_refunds['callback_log_id'] = (int)$callback_log_id;
			$exp = explode("_",$record->fields['transaction_id']);
			$new_refunds['ppp_payment'] = $exp[0];
            if (count($exp)>1)$new_refunds['transaction_id'] = $exp[1];
            $new_refunds['orders_id'] = $record->fields['orders_id'];
            $new_refunds['status'] = 0;
            $new_refunds['ppp_refunded'] = 0;
            $new_refunds['ppp_refund_memo'] = 'retoure';
            $new_refunds['ppp_refunds_type'] = 'Full';
            $new_refunds['total'] = $this->getTotal($new_refunds['orders_id']);
        }

        return $new_refunds;
    }

    public function _getRefunds ($callback_log_id)
    {
        global $db;
        $record = $db->Execute("SELECT ppp_refunds_id FROM " . $this->_table . " WHERE callback_log_id = ? LIMIT 1",array((int)$callback_log_id));
        if ($record->RecordCount() > 0) {
            return $record->fields['ppp_refunds_id'];
        }

        return 0;
    }

    public function _isRefunded ($ppp_refunds_id)
    {
        global $db;
        $record = $db->Execute("SELECT ppp_refunds_id FROM " . $this->_table . " WHERE ppp_refunded = 1 AND success = 1 AND ppp_refunds_id = ? LIMIT 1",array((int)$ppp_refunds_id));
        if ($record->RecordCount() > 0) {
            return true;
        }

        return false;
    }
}

?>