<?php
defined('_VALID_CALL') or die('Direct Access is not allowed.');
global $language;
$js = "ppp_payment_data(".$this->oID.",".$this->orderData['order_customer']['customers_id'].");";
$js .= "function ppp_payment_data(order_id,customers_id){
			
				var conn = new Ext.data.Connection();
				 conn.request({
				 url: '../cronjob.php',
				 method:'GET',
				 params: {'order_id': order_id,'customers_id': customers_id, 'ppp_payment_details':1,'seckey':'"._SYSTEM_SECURITY_KEY."','lang':'".$language->code."'},
				 success: function(responseObject) {
						  var s= JSON.parse(responseObject.responseText);
							if (s.success===true){
							   Ext.MessageBox.alert('Message', '".TEXT_PPP_PAYMENT_INFO."' + '<textarea cols=\'62\' rows=\'15\' style=\' overflow-y:scroll;padding:5px\' >'+s.data+'</textarea>');
							}
							else Ext.MessageBox.alert('Message', '".TEXT_WRONG_SYSTEM_SECURITY_KEY."');
							contentTabs.getActiveTab().getUpdater().refresh();
						  }
				 });
			

		};";

$ppp_payment_details = PhpExt_Button::createTextButton(__define("BUTTON_PPP_PAYMENT_DETAILS"),
		new PhpExt_Handler(PhpExt_Javascript::stm($js)));

$ppp_payment_details->setType(PhpExt_Button::BUTTON_TYPE_BUTTON);
if ($this->orderData["order_data"]["payment_code"]=='xt_paypal_plus') $Panel->addButton($ppp_payment_details);

