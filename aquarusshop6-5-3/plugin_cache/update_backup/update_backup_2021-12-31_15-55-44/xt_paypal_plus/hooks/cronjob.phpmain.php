<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');

	if (isset($_GET['ppp_payment_details']) && ($_GET['ppp_payment_details']==1)){
		include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus.php';
		include_once _SRV_WEBROOT.'plugins/xt_paypal_plus/classes/class.paypal_plus_transactions.php';
		$ppp = new paypal_plus;
		global $db;
		$order_id = $_GET['order_id'];
		$customer_id = $_GET['customers_id'];
		if (($order_id==0) && ($_GET['callback_log_id']>0)){
			$ppp_tr = new paypal_plus_transactions; 
			$r = $ppp_tr->getPaymentDetails($_GET['callback_log_id']);
			$order_id = $r->order_id;
			$customer_id = $r->customers_id;
		}
		if ($order_id>0)
		{
			$rc = $db->Execute("SELECT  PPP_SALEID,PPP_PAYMENTID FROM ".TABLE_ORDERS."  WHERE orders_id = ? ",array($order_id));
			if ($rc->recordCount()>0)
			{
                $obj = new stdClass;
                $obj->success = true;

			    if(empty($rc->fields['PPP_SALEID']) || empty($rc->fields['PPP_PAYMENTID']))
                {
                    $page_data = "PPP_SALEID oder PPP_PAYMENTID nicht gefunden.\nEvtl wurde die Bestellung letztlich nicht über Paypal Plus abgewickelt ?";
                }
			    else
                {
                    $ppp->customer = 'admin_' . time();//$customer_id;
                    $ppp->orders_id = $order_id;
                    $ppp->position = 'admin';
                    $ppp->generateSecurityToken();
                    $res = $ppp->getSaleDetails($rc->fields['PPP_SALEID'], $rc->fields['PPP_PAYMENTID']);
                    $ppp->unsetPayPalPlusSessions($ppp->customer);


                    $page_data = '';
                    foreach ($ppp->PaymentInfo($res) as $k => $v)
                    {
                        //$rc = $db->GetOne("SELECT language_value FROM ".DB_PREFIX."_language_content WHERE language_key=? and language_code=?",array($k,$_GET['lang']));
                        $page_data .= $k . ": " . $v . "\n";
                    }
                }

                $obj->data = $page_data;
				
				echo json_encode($obj);
				die();
			}
		}
	}
