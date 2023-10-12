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
//die();
global $is_cronjob_processing, $db, $filter, $xtPlugin;
$is_cronjob_processing = true;

include 'xtCore/main.php';

if (isset($_GET['feed_id']) || isset($_GET['feed_key'])){
	if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
    {
        echo constant('TEXT_WRONG_SYSTEM_SECURITY_KEY'); return false;
    }

    $feed_id = '';
	if(isset($_GET['feed_id']))
		$feed_id = (int)$_GET['feed_id'];
	elseif(isset($_GET['feed_key']))
		$feed_id = $_GET['feed_key'];

	if((int)strlen($feed_id) == 32) {
		$feed_id = (int)$db->GetOne("SELECT feed_id FROM " . TABLE_FEED . " WHERE feed_key='" . $feed_id . "'");
	}

	if (is_int($feed_id)) {

		$export = new export($feed_id);
		// check if user/pass is required
		if ($export->data['feed_pw_flag']=='1') {
			if ($export->data['feed_pw_user']!=$_GET['user'] || $export->data['feed_pw_pass']!=$_GET['pass']) die('- auth failed -');
		}

		// rewrite price class, rewrite currency class
		unset($customers_status);
		$customers_status = new customers_status($export->data['feed_p_customers_status']);

		if ($export->data['feed_type']=='1') {
			unset($price);
            $price = new price($customers_status->customers_status_id, $customers_status->customers_status_master,$export->data['feed_p_currency_code']);
		}
		$export->_run();
	} else {
		die ('- no id -');
	}
}

if (isset($_GET['imgProc'])) {
    if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
    {
        echo constant('TEXT_WRONG_SYSTEM_SECURITY_KEY'); return false;
    }
			
    $processing = new ImageProcessing();
    $processing->run_processing($_GET);

}

if (isset($_GET['ImportImages'])) {
    if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
    {
        echo constant('TEXT_WRONG_SYSTEM_SECURITY_KEY'); return false;
    }

    $processing = new ImageImporting();
    $processing->run_importing($_GET);

}

if (isset($_GET['export_tlps'])) {
	include 'xtFramework/library/phpxml/xml.php';
	$exp  = new export_tpls();
	$exp->_export();
	
}

//Send order mail
if (isset($_GET['sendordermail']) && $_GET['sendordermail']==1) {
	
	$obj = new stdClass();
	if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
	{
	    $obj->success = false;
		echo json_encode($obj);
		die();
	}
	
    $sent_order_mail  = new order($_GET['order_id'],$_GET['customer_id']);
	$mail_sent = $sent_order_mail->_sendOrderMail();
    $status = $_GET['status'];
    $comments = 'Send Order Mail';
    $customer_notified = 1;
    $show_comments = 1;
    $trigger = 'admin';
    $callback_id = 0;
	$data_array = array();
    $data_array['orders_id']=$_GET['order_id'];
    $data_array['orders_status_id']=$status;
    $data_array['customer_notified']=$customer_notified;
    $data_array['customer_show_comment']=$show_comments; 
    $data_array['comments']=$comments;
    $data_array['change_trigger']=$trigger;
    $data_array['callback_id']=$callback_id;
   
    $db->AutoExecute(TABLE_ORDERS_STATUS_HISTORY,$data_array,'INSERT');
	
	$obj->success = $mail_sent;
	echo json_encode($obj);
}

// Reenable download
if (isset($_GET['reenable_download']) && $_GET['reenable_download']==1) {

	$obj = new stdClass();
	if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
	{
		$obj->success = false;
		echo json_encode($obj);
		die();
	}
	
	$orderId = $filter->_int($_GET['order_id']);
	$db->Execute('UPDATE '.TABLE_ORDERS_PRODUCTS_MEDIA.' SET download_count=0 WHERE orders_id=?', [$orderId]);

	// Log action
	$insert_array = array(
			'download_action' => 2, // 2 - Downloads reenabled
			'download_count' => '',
			'orders_id' => $orderId,
			'attempts_left' => '',
			'file' => '',
	);
	$db->AutoExecute(TABLE_DOWNLOAD_LOG, $insert_array);
	
	$obj->success = true;
	echo json_encode($obj);
}

if (isset($_GET['seo_regenerate']) && $_GET['seo_regenerate']) {
    if (constant('_SYSTEM_SECURITY_KEY')!=$_GET['seckey'])
    {
        echo constant('TEXT_WRONG_SYSTEM_SECURITY_KEY'); return false;
    }
	$regenerate = new seo_regenerate();
	$regenerate->regenerateUrls($_GET['store_id'], $_GET['url_type'], $_GET['offset']);
}


if(defined('TABLE_CRON'))
{
	// once per 60 seconds
	if(empty($_SESSION['cron_cleanup']) || time() + 60 > $_SESSION['cron_cleanup'] )
	{
		// set next run date to NULL for all inactive jobs
		$sql = 'UPDATE ' . TABLE_CRON . ' SET next_run_date=NULL where active_status != 1';

		$db->Execute($sql);
		// check if there active jobs with next run date NULL and last exec time one hour ago
		// that means an error occured during exec, however we recalculate next run date
		$sql = 'select * from ' . TABLE_CRON . ' where  next_run_date IS NULL and running_status = 1 and now()>date_add(last_run_date,interval 15 minute)';
        $arr_cron = $db->getAll($sql);

		if (count($arr_cron) > 0)
		{
			$xt_cron = new xt_cron();

			foreach ($arr_cron as $item)
			{
				$nr = $xt_cron->calc_next_run($item, true);
				$sql = 'update  ' . TABLE_CRON . ' set  next_run_date=?, running_status=0 where cron_id =?';
				$db->Execute($sql, array($nr['next_run_date'], $item['cron_id']));
			}
		}
		$_SESSION['cron_cleanup'] = time();
	}


	if(empty($_REQUEST['executor']) || $_REQUEST['executor']!='admin')
	{
		$sql = 'select count(*) from ' . TABLE_CRON . ' where  next_run_date IS NULL and active_status = 1';
		$crons_running = $db->GetOne($sql);
		if($crons_running == 0)
		{
            if (!empty($_REQUEST['cronstep']))  // warum ist das hier ? für den hook später?
            {
			$or = $_REQUEST['cronstep'] == 1 ? 'or (next_run_date is null)' : '';
			$limit = $_REQUEST['cronstep'] == 1 ? '' : ' limit 1';
			$order = $_REQUEST['cronstep'] == 1 ? '' : ' order by next_run_date';
            }
			$sql = "select * from " . TABLE_CRON . " where next_run_date < NOW() and active_status = 1  order by next_run_date limit 1";
            //$sql = "select * from " . TABLE_CRON . " where next_run_date < DATE_ADD(NOW(), INTERVAL 1 HOUR) and active_status = 1  order by next_run_date limit 1";
			$arr_cron = $db->getAll($sql);

			if (count($arr_cron) > 0)
			{
                unset($_SESSION['cron_cleanup']);
        		$xt_cron = new xt_cron();

				foreach ($arr_cron as $item)
				{
            		$xt_cron->cron_start_by_id($item['cron_id'], true);
        		}
    		}
		}
	}
}

($plugin_code = $xtPlugin->PluginCode('cronjob.php:main')) ? eval($plugin_code) : false;
