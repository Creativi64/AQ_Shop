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

include_once _SRV_WEBROOT.'plugins/xt_ship_and_track/classes/class.xt_ship_and_track.php';

class callback_xt_ship_and_track extends callback {
	
	function process() {

		if ($_REQUEST['target']=='shipcloud')
		{
			$this->data = array();
			$raw_post_data = file_get_contents('php://input');

			$result = json_decode($raw_post_data);

			$msg = 'OK';
			$status_code = 200;

			if ($result && $result->data && $result->data->id)
			{
				global $db;
				$orders_id = $db->GetOne("SELECT ".COL_SHIPCLOUD_XT_ORDER_ID." FROM ".TABLE_SHIPCLOUD_LABELS. " WHERE ".COL_SHIPCLOUD_LABEL_ID." = ? ", array($result->data->id));

				if($orders_id)
                {
                    $st = new xt_ship_and_track();
                    $st->updateStatus(array('orders_id' => $orders_id, 'tracking_code' => $result->data->id));
                }
                else {
                    $status_code = 404;
                    $msg = 'order not found for ['.$result->data->id.']';
                }
			}
			else {
                $status_code = 404;
                $msg = 'no data found in request';
            }
			header("HTTP/1.0 $status_code $msg");
			die("$status_code $msg");
		}
    }
    
}
