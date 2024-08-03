<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

if (isset($_GET['skrill_payment_details']) && ($_GET['skrill_payment_details']==1))
{
	include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_skrill/classes/class.xt_skrill.php';

	global $db;

	$order_id = $_GET['order_id'];
	$trn_id = $_GET['trn_id'];

	$obj = new stdClass;
	$obj->success = true;

	$skrill = new xt_skrill();
	try
	{
		$data = $skrill->getUpdateStatus($trn_id);
		$data_display = array(
			'payment_type' => '',
			//'amount' => '',
			//'mb_amount' => '',
			'currency' => '',
			'transaction_id' => '',
			'mb_transaction_id' => '',
			'country' => '',
			'payment_instrument_country' => '',
			'IP_country' => ''
		);
		foreach($data_display as $k => $v)
		{
			if(array_key_exists($k, $data) || true)
			{
				$key = $db->GetOne("SELECT language_value FROM ".DB_PREFIX."_language_content WHERE language_key=? and language_code=?",array('TEXT_SKRILL_'.$k,$_GET['lang']));
				$val = $data[$k];
				if($k == 'payment_type')
				{
					$val = xt_skrill::$payments[$val]['name'];
				}
				$data_display[$key] = $val;
			}
			unset($data_display[$k]);
		}

		$error = false;
		if(array_key_exists('failed_reason_code', $data))
		{
			$error = xt_skrill::$errors[$data['failed_reason_code'].''];
		}

		$payment_type = xt_skrill::$payments[$data['payment_type']]['name'];

		$tplFile = 'info.tpl.html';
		$template = new Template();
		$template->getTemplatePath($tplFile, 'xt_skrill', 'admin', 'plugin');
		$msg = $db->GetOne("SELECT language_value FROM ".DB_PREFIX."_language_content WHERE language_key=? and language_code=?",array('TEXT_SKRILL_PAYMENT_INFO_SUCCESS',$_GET['lang']));
		$html = $template->getTemplate('', $tplFile, array('info_data' => $data_display, 'msg' => $msg, 'error' => $error));

		$obj->data = $html; //$page_data;
	}
	catch(Exception $e)
	{
		$obj->data = $e->getMessage();
	}

	echo json_encode($obj);
	die();
}
