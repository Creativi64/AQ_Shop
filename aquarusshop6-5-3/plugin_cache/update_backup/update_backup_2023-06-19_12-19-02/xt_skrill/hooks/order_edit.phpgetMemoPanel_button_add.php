<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if ($this->orderData["order_data"]["payment_code"]=='xt_skrill')
{
    global $language;
    $js = "skrill_payment_data('".$this->oID."','".$this->orderData['order_data']['orders_data']."');";
    $js .= "function skrill_payment_data(order_id,trn_id){

				 var conn = new Ext.data.Connection();
				 conn.request({
				 url: '../cronjob.php',
				 method:'GET',
				 params: {'order_id': order_id,'trn_id': trn_id, 'skrill_payment_details':1,'seckey':'"._SYSTEM_SECURITY_KEY."','lang':'".$language->code."'},
				 success: function(responseObject) {
						  var s= JSON.parse(responseObject.responseText);
							if (s.success===true){
								var minwidth = Ext.Msg.minWidth;
							   Ext.Msg.minWidth = 400;
							   Ext.MessageBox.alert('Skrill', s.data);
							   Ext.Msg.minWidth = minwidth;
							}
							else Ext.MessageBox.alert('Message', '".TEXT_WRONG_SYSTEM_SECURITY_KEY."');
							contentTabs.getActiveTab().getUpdater().refresh();
						  }
				 });


		};";

    $skrill_payment_details = PhpExt_Button::createTextButton(__define("BUTTON_SKRILL_PAYMENT_DETAILS"),
        new PhpExt_Handler(PhpExt_Javascript::stm($js)));

    $skrill_payment_details->setType(PhpExt_Button::BUTTON_TYPE_BUTTON);

    $Panel->addButton($skrill_payment_details);
}
