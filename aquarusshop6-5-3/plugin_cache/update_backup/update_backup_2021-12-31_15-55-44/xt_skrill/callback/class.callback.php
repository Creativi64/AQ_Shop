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

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/hooks/page_registry.php_bottom.php';
include_once _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'classes/class.callback.php';

class callback_xt_skrill extends callback {

	var $version = '2.0';

	private $_isAdminAction = false;

	function process() {
		global $filter;

		if (!is_array($_POST)) return;

		$this->data = array();
		foreach ($_POST as $key => $val) {
			$this->data[$key] = $filter->_filter($val);
		}
		$accepted_request_params = array('msg_type', 'refund_type');
		foreach($_REQUEST as $key => $val) {
			if (in_array($key, $accepted_request_params))
				$this->data[$key] = $filter->_filter($val);
		}

		$response = $this->_callbackProcess();

		if ($response->repost) {
			header('HTTP/1.0 404 Not Found');
		} else {
			header("HTTP/1.0 200 OK");
		}
	}

	function processAdminAction($data) {
		global $filter;

		if (!is_array($data)) return;

		$this->data = array();
		foreach ($data as $key => $val) {
			$this->data[$key] = $filter->_filter($val);
		}
		$this->log_callback_data = true;
		$this->_isAdminAction = true;
		return $this->_callbackProcess();
	}


	function _callbackProcess()
	{
		global $db;

		// order ID already inserted ?
		$success = $this->_getOrderID();
		if (!$success)
		return false;

		if ($this->log_callback_data == true) {
			$log_data = array();
			$log_data['module'] = 'xt_skrill';
			$log_data['class'] = 'callback_data';
			$log_data['orders_id'] = $this->orders_id;
			$log_data['transaction_id'] = $this->data['transaction_id'];
			$log_data['callback_data'] = serialize($this->data);
			$this->_addLogEntry($log_data);
		}

		// check if merchant ID matches
		$success = $this->_checkMerchantMail();

		if (!$success)
		return false;

		// validate md5signature
		$success = $this->_checkMD5Signature();
		if (!$success)
		return false;
/*
		// validate Amount
		$success = $this->_checkAmount();
		if (!$success)
		return false;
*/
		$status = $this->data['status'];
		// change status depending on merchant_fields send in api
		// process refund
		if($this->data['msg_type'] == 'refund_notification')
		{
			// when refunding we are sending refund_type
			$status = $this->data['refund_type'] == 'full' ? 50 : 51;
		}

		$this->_setStatus($status);
	}

	/**
	 * compare orders total amount with ipn amount
	 *
	 * @return boolean
	 */
	function _checkAmount() {
		global $db;

		$order = new order($this->orders_id,$this->customers_id);

		if ($order->order_total['total']['plain']==$this->data['mb_amount']) return true;

		$log_data = array();
		$log_data['module'] = 'xt_skrill';
		$log_data['class'] = 'error';
		$log_data['orders_id'] = $this->orders_id;
		$log_data['error_msg'] = 'amount conflict';
		$log_data['error_data'] = serialize(array('detail'=>'Amount SEND:' . $this->data['mb_amount'] . ' Amount STORED:' . $order->order_total['total']['plain']));
		$this->_addLogEntry($log_data);

		return false;
	}

	/**
	 * Find order ID for given MB transaction_id
	 *
	 * @return boolean
	 */
	function _getOrderID() {
		global $db;

		$order_query = "SELECT orders_id,customers_id FROM ".TABLE_ORDERS." WHERE orders_data = ?";
		$rs = $db->Execute($order_query, array($this->data['transaction_id']));

		if ($rs->RecordCount() == 1) {
			$this->orders_id = $rs->fields['orders_id'];
			$this->customers_id = $rs->fields['customers_id'];
			return true;
		}

		$log_data = array();
		$log_data['module'] = 'xt_skrill';
		$log_data['class'] = 'error';
		$log_data['error_msg'] = 'order id not found';
		$log_data['error_data'] = serialize(array('transaction_id'=>$this->data['transaction_id']));
		$this->_addLogEntry($log_data);
		$this->repost = true;
		return false;

	}

	/**
	 * check if merchant ID and mechant mail matches to DB
	 *
	 * @return boolean
	 */
	function _checkMerchantMail() {

		// does merchant ID exists ?
		if (!isset ($this->data['merchant_id']) || $this->data['merchant_id'] != XT_SKRILL_MERCHANT_ID) {
			$log_data = array();
			$log_data['module'] = 'xt_skrill';
			$log_data['class'] = 'error';
			$log_data['orders_id'] = $this->orders_id;
			$log_data['error_msg'] = 'merchant id conflict';
			$log_data['error_data'] = serialize(array('detail'=>'Merchant ID SEND:' . $this->data['merchant_id'] . ' Merchant ID STORED:' . XT_SKRILL_MERCHANT_ID));
			$this->_addLogEntry($log_data);
			return false;
		}
		// merchant mail ?
		if (!isset ($this->data['pay_to_email']) || strtolower($this->data['pay_to_email']) != strtolower(XT_SKRILL_EMAILID)) {
			$log_data = array();
			$log_data['module'] = 'xt_skrill';
			$log_data['class'] = 'error';
			$log_data['orders_id'] = $this->orders_id;
			$log_data['error_msg'] = 'merchant email conflict';
			$log_data['error_data'] = serialize(array('Merchant EMAIL SEND:' . $this->data['pay_to_email'] . ' Merchant EMAIL STORED:' . XT_SKRILL_EMAILID));
			$this->_addLogEntry($log_data);
			return false;
		}

		return true;
	}

	/**
	 * Calculate and check MD5 Signature of Callback
	 *
	 * @return boolean
	 */
	function _checkMD5Signature() {

		if (XT_SKRILL_MERCHANT_SECRET == '')
		return true;

		$secret = XT_SKRILL_MERCHANT_SECRET;
		$md5sec = strtoupper(md5($secret));
		$hash = $this->data['merchant_id'] . $this->data['transaction_id'] . $md5sec . $this->data['mb_amount'] . $this->data['mb_currency'] . $this->data['status'];
		$hash = strtoupper(md5($hash));
		if ($hash != $this->data['md5sig']) {
			$this->Error = '1004';
			$log_data['module'] = 'xt_skrill';
			$log_data['class'] = 'error';
			$log_data['orders_id'] = $this->orders_id;
			$log_data['error_msg'] = 'md5 check failed';
			$this->_addLogEntry($log_data);
			return false;
		}

		return true;

	}

	function _setStatus($skrill_msg_status)
	{
		$send_mail = false;

		//error_log('cb set status => '.$skrill_msg_status);

		$log_data = array();
		$log_data['orders_id'] = $this->orders_id;
		$log_data['module'] = 'xt_skrill';
		$log_data['class'] = 'success';
		$log_data['transaction_id'] = $this->data['transaction_id'];

		switch ($skrill_msg_status) {

			// processed
			case 2 :
				$status = XT_SKRILL_PROCESSED;
				$log_data['callback_data'] = array('message'=>'OK','error'=>'200','transaction_id'=>$this->data['mb_transaction_id']);
				$send_mail = true;
				break;

			// canceled
			case -2 :
			case -1 :
				$status = XT_SKRILL_CANCELED;
				$log_data['callback_data'] = array('message'=>'FAILED','error'=>'999','transaction_id'=>$this->data['mb_transaction_id']);
				break;

			// pending
			case 0 :
			case 1 :
				$status = XT_SKRILL_PENDING;
				$log_data['callback_data'] = array('message'=>'PENDING','error'=>'200','transaction_id'=>$this->data['mb_transaction_id']);
				break;

			// chargeback
			case -3:
				$status = XT_SKRILL_CHARGEBACK;
				$log_data['callback_data'] = array('message'=>'OK','error'=>'200','transaction_id'=>$this->data['mb_transaction_id']);
				$send_mail = true;
				break;

			case 50 : // refunded   		local defined values
			case 51 : // partial refund
				$status = $skrill_msg_status == 50 ? XT_SKRILL_REFUNDED : XT_SKRILL_REFUNDED_PARTIAL;
				$msg = $skrill_msg_status == 50 ? 'full refund' : 'partial refund';
				$log_data['class'] = 'success_refund';
				$log_data['callback_data'] = array('message'=>$msg,'error'=>'200','transaction_id'=>$this->data['mb_transaction_id']);

				global $db;
				if ($this->data['status'] == 2)
				{
					$txn_log_id = $this->_addLogEntry($log_data);
					$db->Execute("UPDATE " . TABLE_SKRILL_REFUNDS . ' SET refunded = 1, success = 1, callback_log_id=?, callback_data=? WHERE refunds_id=?', array($txn_log_id, serialize($this->data), (int)$this->data['refunds_id']));
				}

				break;

			default:
				$log_data['callback_data'] = array('message'=>'UNKNOWN STATUS','error'=>'200','transaction_id'=>$this->data['mb_transaction_id']);
				$this->_addLogEntry($log_data);
				return;

		}
		if ($status<50 || ($status>=50 && $this->data['status']!=2))
		{
			$txn_log_id = $this->_addLogEntry($log_data);
		}

		// update order status
		$this->_updateOrderStatus($status, $send_mail, $txn_log_id);
	}

	function _updateOrderStatus($new_order_status, $send_mail='true', $callback_id='', $callback_message='') {
		$order = new order($this->orders_id,$this->customers_id);
		$send_mail = $send_mail && $order->order_data['orders_status_id']!=$new_order_status;
		if ($callback_id==null) $callback_id='';
		$order->_updateOrderStatus($new_order_status,'',$send_mail ? 'true' : false,'true',$this->_isAdminAction ? 'admin':'IPN', $callback_id, $callback_message);
	}
}