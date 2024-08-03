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

include_once '../xtFramework/admin/main.php';

global $xtc_acl;

if (!$xtc_acl->isLoggedIn()) {
	die('login required');
}

include (_SRV_WEBROOT_ADMIN.'page_includes.php');


if(array_value($_GET,'action') == 'saveHistory'){

	$tmp_oid = (int) $_POST['edit_id'];
	$obj = new stdClass;

	if($_POST['order_status']!='' && $_POST['actual_status']!=$_POST['order_status']){

		$update_order = new order($tmp_oid,-1);

	//	__debug($update_order);

		if($_POST['comments_send']){
			$send_comments='true';
		}else{
			$send_comments='false';
		}

		if($_POST['customers_send']){
			$send_email='true';
		}else{
			$send_email='false';
		}

		$update_order->_updateOrderStatus((int)$_POST['order_status'], $_POST['order_comments'], $send_email, $send_comments, $_SESSION["admin_user"]["user_name"]);
		global $xtPlugin;
        ($plugin_code = $xtPlugin->PluginCode('order_edit.php:saveHistory')) ? eval($plugin_code) : false;
		$obj->success = true;

	}else{

		$obj->success = false;

	}

    ($plugin_code = $xtPlugin->PluginCode('order_edit.php:saveHistory_bottom')) ? eval($plugin_code) : false;
	header("Content-Type: application/json");
	echo json_encode($obj);
	die;
}


if ($_GET['edit_id'])
$oID = (int)$_GET['edit_id'];
if (!is_int($oID)) die('no order id');

class order_templates {
	function __construct() {

	}
	function getPriceTemplate ($name, $type = 'formated')
    {
	    global $xtPlugin;

		$tpl = '<tpl for="'.$name.'">';
		$tpl.= '{formated}';
		if ($type == 'taxpercent')
		$tpl.= '({taxpercent}%)<br/>';
		$tpl.= '</tpl>';

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
		return $tpl;
	}

	function depreciated_getTotalTemplate($name, $type = '') {
		if ($type == 'total') {
			$tpl = '<tpl for="'.$name.'">';
			$tpl.= '<td class="tdtotal">{text}: </td>';
			$tpl.= '<td class="tdtotal">'.$this->getPriceTemplate('price').'</td>';
			$tpl.= '</tpl>';

		} else {
			$tpl = '<tpl for="'.$name.'">';
			$tpl.= '{text}: ';
			$tpl.= $this->getPriceTemplate('price', $type);
			$tpl.= '</tpl>';
		}
		return $tpl;
	}

	function getAdressTemplate($name)
    {
        global $xtPlugin;

        $tpl = '<tpl for="'.$name.'">';

		$tpl.= '{firstname} {lastname}<br />';
		$tpl.= '<tpl if="company !=null">{company}<br /></tpl>';
		$tpl.= '{street_address}<br />';
		$tpl.= '<tpl if="suburb !=null">{suburb}<br /></tpl>';
		$tpl.= '{country_code} {postcode} {city}<br />';
		$tpl.= '<tpl if="zone !=null">({zone})</tpl>';

		$tpl.= '</tpl>';

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
		return $tpl;

	}

	function getInfoTemplate()
    {
        global $xtPlugin;

        $tpl = '<if:infos><tpl for="infos">';

		$tpl.= '{text}: {value}<br />';

		$tpl.= '</tpl></if>';

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
		return $tpl;

	}

	function getHistoryTemplate()
    {
        global $xtPlugin;

        $headerTemplate = new PhpExt_XTemplate(
				'<tpl >',
				'<div class="row"><div class="col-xs-12 table-responsive">',
                '<span class="h5">'.__define('TEXT_ORDER_STATUS').'</span>',
				'<table class="table table-striped orderstatushistory"><thead><tr>',
				'<th width="150">'.__define('TEXT_DATE_ADDED').'</th>',
				'<th width="150">'.__define('TEXT_ORDERS_STATUS').'</th>',
				'<th width="100">'.__define('TEXT_CUSTOMER_NOTIFIED').'</th>',
				'<th width="150">'.__define('TEXT_TRIGGERED_BY').'</th>',
				'<th width="150">IPN ID</th>',
				'<th width="150">IPN Message</th>',
				'<th>'.__define('TEXT_comments').'</th>',
				'</tr></thead><tbody><tpl for="data" ><tr valign="top">',
				'<td>{date_added}&nbsp;</td>',
				'<td>{orders_status}&nbsp;</td>',
				'<td align="center">',
					'<tpl if="customer_notified ==1"><img src="images/icons/accept.png" qtip="'.__define('TEXT_CUSTOMERS_SEND').'"/></tpl><tpl if="customer_notified ==1 && customer_show_comment ==1"><img src="images/icons/accept.png" qtip="'.__define('TEXT_COMMENTS_SEND').'"/></tpl>',
        			'<tpl if="customer_notified ==0"><img src="images/icons/delete.png" qtip="'.__define('TEXT_CUSTOMERS_SEND').'"/></tpl>',
        		'</td>',
				'<td>{change_trigger}</td>',
				'<td>{callback_id}</td>',
				'<td>{callback_message}</td>',
				'<td>{comments}&nbsp;</td>',
				'</tr></tpl></table></div></div>',
				'</tpl>'
		);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
        return $headerTemplate;
	}

    function getDownloadsTemplate()
    {
        global $xtPlugin;


        // '<th width="150">'.__define('TEXT_FILE_DOWNLOADS').'</th>',

        $headerTemplate = new PhpExt_XTemplate(
            '<tpl >',
            '<div class="row"><div class="col-xs-12 table-responsive">',
            '<span class="h5">'.__define('TEXT_FILE_DOWNLOADS').'</span>',
            '<table class="table table-striped orderDownloadsLogs"><thead><tr>',
            '<th width="150">'.__define('TEXT_ACTION').'</th>',
            '<th width="100">'.__define('TEXT_COUNT').'</th>',
            '<th width="150">'.__define('TEXT_DATE_TIME').'</th>',
            '<th width="150">'.__define('TEXT_ATTEMPTS_LEFT').'</th>',
            '<th width="150">'.__define('TEXT_FILENAME').'</th>',
            '</tr></thead><tbody><tpl for="data" ><tr valign="top">',
            '<td>{download_action}&nbsp;</td>',
            '<td>{download_count}&nbsp;</td>',
            '<td>{log_datetime}</td>',
            '<td>{attempts_left}</td>',
            '<td>{file}</td>',
            '</tr></tpl></table></div></div>',
            '</tpl>'
        );

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
        return $headerTemplate;
    }


    function depreciated_getHeaderTemplate() {
		$headerTemplate = new PhpExt_XTemplate(
            '<tpl for="order_data" >',
				'<div class="ordertitle">',
					'<div class="floatright"><tpl if="orders_status !=null">{orders_status}</tpl><br /><tpl if="last_modified !=null">'.__define('TEXT_last_modified').' {last_modified}</tpl></div>',
                	'<h3>'.__define('TEXT_ORDER_ID').': {orders_id}&nbsp;&nbsp;({date_purchased})</h3>',
					'<tpl if="orders_date_finished !=null">{orders_date_finished}<br /></tpl>',
					'{orders_total_name}',
				'</div>',
				'<div class="orderdetail"><table ><thead><tr>',
					'<th>'.__define('TEXT_SHOP').': {shop_id}</th>',
					'<th>'.__define('TEXT_DELIVERY_ADDRESS').'</th>',
					'<th>'.__define('TEXT_BILLING_ADDRESS').'</th>',
					'<th>'.__define('TEXT_ORDER_INFOS').'</th>',
				'</tr><tr valign="top"><td>',
                		'<p>'.__define('TEXT_customers_email_address').': {customers_email_address}</p>',
                		'<tpl if="customers_cid !=\\\'\\\'"><p>'.__define('TEXT_customers_cid').': {customers_cid}</p></tpl>',
                		'<tpl if="customers_vat_id !=\\\'\\\'"><p>'.__define('TEXT_customers_vat_id').': {customers_vat_id}</p></tpl>',
						'<tpl if="payment_code !=\\\'\\\'"><p>'.__define('TEXT_payment_code').': {payment_code}</p></tpl>',
						'<tpl if="shipping_code !=\\\'\\\'"><p>'.__define('TEXT_shipping_code').': {shipping_code}</p></tpl>',
					'</td>',
					'<td><p class="deliverybg">'.$this->getAdressTemplate('delivery').'</p></td>',
					'<td><p class="billingbg">'.$this->getAdressTemplate('billing').'</p></td>',
					'<td><p class="infobg">'.$this->getInfoTemplate().'</p></td>',
            	'</tr></table></div>',
                '<tpl if="comments !=null"><p>'.__define('TEXT_comments').': {comments}</p></tpl>',
            '</tpl>'
            );
            return $headerTemplate;
	}


	function getProductsTemplate()
    {
        global $xtPlugin;

		$extras = '';


		($plugin_code = $xtPlugin->PluginCode('order_edit.php:getProductsTemplate_top')) ? eval($plugin_code) : false;


		$productsTemplate = new PhpExt_XTemplate($extras);

		return $productsTemplate;
	}

}

class admin_order_edit extends order_templates {

    public $order_data, $orderData, $oID, $order;

	function __construct($oID) {
		global $xtPlugin;
		
		($plugin_code = $xtPlugin->PluginCode(__CLASS__.':_construct_top')) ? eval($plugin_code) : false;
		
		$this->oID = $oID;

		$this->setOrderData ($oID);
	}

	function setOrderData ($oID) {
		global $language, $xtPlugin;
		$order = new order($oID);
		$order_data = $order->_buildData($oID);
		$this->order = $order;
        foreach ($language->_getLanguageList('admin') as $_cache_language_list_Item) {
            if ($_cache_language_list_Item['code'] == $order_data['order_data']['language_code']) {
                $order_data['order_data']['language_text'] = $_cache_language_list_Item['text'];
            }
        }

        if (!is_array($order_data['order_data']) || !array_key_exists('language_text', $order_data['order_data']))
            $order_data['order_data']['language_text'] = '-';

        $total_weight = 0;
        foreach ($order_data['order_products'] as $op)
        {
            $total_weight += $op['products_weight'] * $op['products_quantity'];
        }
        $order_data['order_total']['total_weight'] = $total_weight;

        $order_data['coupon_info'] = false;
        global $db, $order_edit_controller;
        if(class_exists('xt_coupons') && $order_edit_controller)
        {
            $order_edit_edit_coupon = new order_edit_edit_coupon();

            $couponCode = $current_couponCode_show = '';
            $couponId = (int)$db->GetOne(
                "SELECT `coupon_id` FROM `" . DB_PREFIX . "_coupons_redeem` WHERE `order_id` = ?",
                array($order->oID)
            );
            if ($couponId)
            {
                $couponCode = $db->GetOne(
                    "SELECT `coupon_code` FROM `" . DB_PREFIX . "_coupons` WHERE `coupon_id` = ?",
                    array($couponId)
                );

                $oci = $order_edit_controller->getCouponForOrder($order->oID);
                if ($oci->isToken)
                {
                    $couponCode = $oci->xt_coupon_token['coupon_token_code'];
                }
                $table_data = new adminDB_DataRead(TABLE_COUPONS, TABLE_COUPONS_DESCRIPTION, null, 'coupon_id', '', '1', '', '');
                $dbdata = $table_data->getData($couponId);
                if (is_array($dbdata))
                {
                    $descData = $order_edit_edit_coupon->getCouponDescription($dbdata[0]);
                    $current_couponCode_show = $couponCode . '  (' . $descData['name'] . ')';

                    $order_data['coupon_info'] = [
                        'current_coupon_code' => $couponCode,
                        'current_coupon_code_show' => $current_couponCode_show
                    ];
                }
            }

        }

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':this_order_data')) ? eval($plugin_code) : false;

		$this->order_data = $order_data;

		
		foreach ($order_data['order_data'] as $key => $value) {

			if (preg_match('/delivery_/', $key)) {

				$nKey = str_replace('delivery_','',$key);
				$new_order_data['delivery'][$nKey] = $value;
			} elseif (preg_match('/billing_/', $key)) {
				$nKey = str_replace('billing_','',$key);
				$new_order_data['billing'][$nKey] = $value;
			} else {
				$new_order_data[$key] = $value;
			}
		}


		$order_data['order_data'] = $new_order_data;

		$order_data['info'] = $order_data['order_data']['orders_data'];
		$order_data['order_data']['infos'] = array();

		if($order_data['info']){
			$order_data['info'] = unserialize($order_data['info']);
			if (is_array($order_data['info'])) {
			foreach ($order_data['info'] as $ikey => $ivalue) {

				$order_data['order_data']['infos'][] = array('text'=>__define('TEXT_'.strtoupper($ikey)), 'value'=>$ivalue);

			}
			}
		}

		$order_data['total'] = $order_data['order_total']['total'];

		foreach ($order_data['order_total'] as $key => $value) {


			$gkey = '';
			$pkey = '';

			if (preg_match('/product/', $key))
			$pkey = 'product';
			elseif (preg_match('/data/', $key))
			$pkey = 'data';
			elseif (preg_match('/total/', $key))
			$pkey = 'total';

			if (preg_match('/_tax/', $key) && !preg_match('/_tax_rate/', $key))
			$gkey = 'tax';
			elseif (preg_match('/_otax/', $key) && !preg_match('/_tax_rate/', $key))
			$gkey = 'otax';
			elseif (preg_match('/_total/', $key) && !preg_match('/_tax_rate/', $key))
			$gkey = 'total';

			if (preg_match('/_tax/', $key) && !preg_match('/_tax_rate/', $key)) {

				if (is_array($value)) {
					foreach ($value as $vkey => $vval) {

						if ($vval['tax_value']) {
							$new_order_total[$pkey][$gkey]['text'] =  __define('TEXT_'.$key);
							$vval['tax_value']['taxpercent'] = $vval['tax_key'];
							$new_order_total[$pkey][$gkey]['price'][] =  $vval['tax_value'];

						}

					}

				}

			} else {
				$new_order_total[$pkey][$gkey]['text'] =  __define('TEXT_'.$key);
				$new_order_total[$pkey][$gkey]['price'][] =  $value;
			}


		}



		$order_data['order_total'] = $new_order_total;
		$order_data['order_total']['total']['sum']['price'][] = $order_data['total'];


        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':this_orderData')) ? eval($plugin_code) : false;

		//__debug($order_data);
		$this->orderData = $order_data;

		$cust_status = new customers_status();
		$this->order_data['order_customer']['customers_status_name'] = array_value($this->order_data['order_customer'], 'customers_status') ? $cust_status->getGroupName($this->order_data['order_customer']['customers_status']) : 'group-not-exists';
		$this->order_data['order_data']['customers_status_name'] = $cust_status->getGroupName($order_data['order_data']['customers_status']);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

	}

	function getOrderData() {
        return $this->orderData;
	}

	protected function getOrderDownloadsLog()
    {
	    global $xtPlugin;
		$data = $this->orderData['order_download_log'];
		$obj = new stdClass;
        $obj->totalCount = is_array($data) ? count($data) : 0;
        $obj->data = empty($data) ? array() : $data;

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;
		
		return $obj;
	}
	
	function getOrderHistory() {
		global $system_status, $xtPlugin;

		//$table_data = new adminDB_DataRead(TABLE_ORDERS_STATUS_HISTORY, '', '', 'orders_id');
		//$data = $table_data->getData($this->oID);
		$data = $this->orderData['order_history'];
        if(!is_array($data))
        {
            $data = array();
        }

		if(count($data)>0){
			foreach ($data as $key => $oh) {
				// fix for order - edit (nl2br dosn't work)
                if(empty($data[$key]['callback_id'])) $data[$key]['callback_id'] = '';
                $data[$key]['comments'] = str_replace(array("\r\n","\n","\r"),'<br />', $data[$key]['comments']);
				$data[$key]['orders_status'] = $system_status->values['order_status'][$oh['orders_status_id']]['name'];
			}
		}

		$obj = new stdClass;
		$obj->totalCount = count($data);
		$obj->data = $data;

		($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

		return $obj;
		//return array('data' => array(array('date_added' => 'xxx')));
	}


	function getLayout() {
	    global $xtPlugin;

	    $containers = [
	        'headerContainer' => [],
            'buttonContainer' => ['class' => 'order', 'style' => 'width:100%'],
            'overviewContainer' => ['class' => 'order', 'style' => 'margin-right:10px'],
            'overviewContainerAdditions' => ['class' => 'order', 'style' => 'padding:10px'],
            'productsContainer' => ['class' => 'order'],
            'orderhistoryContainer' => ['class' => 'order'],
            'orderDownloadsContainer' => ['class' => 'order', 'style' => 'margin-right:10px'],
            'memoContainer' => ['style' => 'margin-right:10px'],
        ];

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':containers')) ? eval($plugin_code) : false;

        foreach($containers as $k => $container_attributes)
        {
            $attr_html = "";
            if(is_array($container_attributes))
            {
                foreach($container_attributes as $ka => $v)
                {
                    $attr_html .= "{$ka}=\"{$v}\" ";
                }
            }

            $containers[$k] = '<div id="'.$k.$this->oID.'" '.$attr_html.'></div>';
        }

        $html = implode(PHP_EOL, $containers);

        return $html;

        /*
		return '<div id="headerContainer"></div>
                <div class="order" id="buttonContainer'.$this->oID.'" style="width:100%;"></div>
                <div class="order" id="overviewContainer'.$this->oID.'"></div>
                <div class="order" id="overviewContainerAdditions'.$this->oID.'" style="padding:10px;"></div>
                <div class="order" id="productsContainer'.$this->oID.'"></div>
                <div class="order" id="orderhistoryContainer'.$this->oID.'"></div>
                <div class="order" id="orderDownloadsContainer'.$this->oID.'"></div>
                <div id="memoContainer'.$this->oID.'"></div>';
        */
	}

	function getMemoPanel() {
		global $xtPlugin;

        $js='';
		
		$Panel = new PhpExt_Form_FormPanel('memoFormPanel');
		$Panel->setTitle(__define('TEXT_MEMO'))->setId('memoForm'.$this->oID);
		$Panel->setAutoWidth(true);
		$Panel->setBodyStyle('padding: 5px;');

		$Panel->setUrl('order_edit.php?action=saveHistory&edit_id='.$this->oID) ;

		$Panel->addItem(PhpExt_Form_TextArea::createTextArea('order_comments', __define('TEXT_ORDER_COMMENTS'))->setWidth(500));

		$Panel->addItem(PhpExt_Form_Hidden::createHidden('edit_id', $this->oID));
		$Panel->addItem(PhpExt_Form_Hidden::createHidden('actual_status', $this->orderData['orders_status'] ?? 0));

		$eF = new ExtFunctions();
		$combo = $eF->_comboBox('order_status', __define('TEXT_ORDER_STATUS'), 'DropdownData.php?systemstatus=order_status');
		$Panel->addItem($combo);

		$check = PhpExt_Form_Checkbox::createCheckbox('customers_send', __define('TEXT_CUSTOMERS_SEND'));
		$check->setCssClass("checkBox");
		$Panel->addItem($check);

		$check = PhpExt_Form_Checkbox::createCheckbox('comments_send', __define('TEXT_COMMENTS_SEND'));
		$check->setCssClass("checkBox");
		$Panel->addItem($check);

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':before_buttons')) ? eval($plugin_code) : false;
		
		$submitBtn = PhpExt_Button::createTextButton(__define("BUTTON_SAVE"),
		new PhpExt_Handler(PhpExt_Javascript::stm("Ext.getCmp('memoForm".$this->oID."').getForm().submit({
													waitMsg:'Saving Data...',
												    success: function(form,action) {
                                                        var s= JSON.parse(action.response.responseText);
                                                        if (typeof  s.message !='undefined')
                                                            Ext.Msg.alert('".__define('TEXT_ALERT')."',s.message); 
                                                        else Ext.Msg.alert('".__define('TEXT_ALERT')."','".__define('TEXT_SUCCESS')."'); 
												        contentTabs.getActiveTab().getUpdater().refresh()
													}
												   })"))
		);
		
		//  $submitBtn = PhpExt_Button::createTextButton("Submit");
		$submitBtn->setType(PhpExt_Button::BUTTON_TYPE_SUBMIT);
		$Panel->addButton($submitBtn);
		if (!isset($this->orderData['order_customer']['customers_id'])) $this->orderData['order_customer']['customers_id']=0;
		$js .= "Ext.Msg.show({
		   title:'".TEXT_START."',
		   msg: '".TEXT_START_ASK."',
		   buttons: Ext.Msg.YESNO,
		   animEl: 'elId',
		   fn: function(btn){sendmail('".$this->oID."','".(int)$this->orderData['order_customer']['customers_id']."','".$this->orderData['order_data']['orders_status_id']."',btn);},
		   icon: Ext.MessageBox.QUESTION
		});";
		
		$js .= "function sendmail(order_id, customer_id, status, btn){
			  		if (btn == 'yes') {
			  			var conn = new Ext.data.Connection();
		                 conn.request({
		                 url: '../cronjob.php',
		                 method:'GET',
		                 params: {'order_id': order_id, 'customer_id': customer_id, 'status': status, 'sendordermail':1,'seckey':'"._SYSTEM_SECURITY_KEY."'},
		                 success: function(responseObject) {
		                 		  var s= JSON.parse(responseObject.responseText);
									if (s.success===true)
									   Ext.MessageBox.alert('Message', '".TEXT_EMAIL_SENT."');
									else Ext.MessageBox.alert('Message', '".ERROR_EMAIL_SEND." check Logs');
									contentTabs.getActiveTab().getUpdater().refresh();
		                          }
		                 });
					}
		
				};";
		
		$sendOrderMailBtn = PhpExt_Button::createTextButton(__define("BUTTON_SEND_ORDER_MAIL"),
		new PhpExt_Handler(PhpExt_Javascript::stm($js)));
		
		//  $submitBtn = PhpExt_Button::createTextButton("Submit");
		$sendOrderMailBtn->setType(PhpExt_Button::BUTTON_TYPE_BUTTON);
		$Panel->addButton($sendOrderMailBtn);
		
		$js .= "Ext.Msg.show({
		   title:'".TEXT_START."',
		   msg: '".TEXT_REENABLE_DOWNLOAD."',
		   buttons: Ext.Msg.YESNO,
		   animEl: 'elId',
		   fn: function(btn){reenableDownload(".$this->oID.",btn);},
		   icon: Ext.MessageBox.QUESTION
		});";
		
		$js .= "function reenableDownload(order_id, btn){
			  		if (btn == 'yes') {
			  			var conn = new Ext.data.Connection();
		                 conn.request({
		                 url: '../cronjob.php',
		                 method:'GET',
		                 params: {'order_id': order_id, 'reenable_download':1,'seckey':'"._SYSTEM_SECURITY_KEY."'},
		                 success: function(responseObject) {
		                 		  var s= JSON.parse(responseObject.responseText);
									if (s.success===true)
									   Ext.MessageBox.alert('Message', '".TEXT_DOWNLOAD_REENABLED."');
									else Ext.MessageBox.alert('Message', '".TEXT_WRONG_SYSTEM_SECURITY_KEY."');
									contentTabs.getActiveTab().getUpdater().refresh();
		                          }
		                 });
					}
		
				};";

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom_js')) ? eval($plugin_code) : false;
		
		$reenableDownloadsButton = PhpExt_Button::createTextButton(__define("BUTTON_REENABLE_DOWNLOAD"),
				new PhpExt_Handler(PhpExt_Javascript::stm($js)));
		
		$reenableDownloadsButton->setType(PhpExt_Button::BUTTON_TYPE_BUTTON);
		$Panel->addButton($reenableDownloadsButton);

		($plugin_code = $xtPlugin->PluginCode('order_edit.php:getMemoPanel_button_add')) ? eval($plugin_code) : false;

		return $Panel;
	}

	function displayJS () {
		global $xtPlugin, $info;

		($plugin_code = $xtPlugin->PluginCode('order_edit.php:display_top')) ? eval($plugin_code) : false;

		$productsTemplate = $this->getProductsTemplate();
		$historyTemplate = $this->getHistoryTemplate();
		$downloadsTemplate = $this->getDownloadsTemplate();

		$productsPanel = new PhpExt_Panel();
		//$productsPanel->setTitle(__define('TEXT_PRODUCTS'))
		$productsPanel->setAutoWidth(true)
			->setHtml($productsTemplate)->setAutoLoad(false);

		$productsPanel->setRenderTo(PhpExt_Javascript::variable("Ext.get('productsContainer".$this->oID."')"));

		$historyPanel = new PhpExt_Panel();
		//$historyPanel->setTitle(__define('TEXT_PRODUCTS'));
		$historyPanel->setAutoWidth(true)
			->setHtml($historyTemplate)->setAutoLoad(false);
		$historyPanel->setRenderTo(PhpExt_Javascript::variable("Ext.get('orderhistoryContainer".$this->oID."')"));

		$downloadsPanel = new PhpExt_Panel();
		$downloadsPanel->setAutoWidth(true)->setHtml($downloadsTemplate)->setAutoLoad(false);
		$downloadsPanel->setRenderTo(PhpExt_Javascript::variable("Ext.get('orderDownloadsContainer".$this->oID."')"));
        $downloadsJs = '';
		
		$memoFormPanel = $this->getMemoPanel();

        $memoTabPanel = new PhpExt_TabPanel();
        $memoTabPanel->addItem($memoFormPanel);
        $memoTabPanel->setId('memoTabPanel'.$this->oID)
            ->setActiveTab(0)->setAutoHeight(true)->setBodyStyle('min-height:250px;overflow:visible')
            ->setDeferredRender(true);


        $layoutMemoPanel = new PhpExt_Panel();
        $layoutMemoPanel->setLayout(new PhpExt_Layout_BorderLayout())
            ->setId('memoPanel'.$this->oID)
            ->setAutoWidth(false)->setAutoHeight(false)->setBodyStyle('min-height:350px;')
            ->addItem($memoTabPanel, PhpExt_Layout_BorderLayoutData::createCenterRegion())
            ->setRenderTo(PhpExt_Javascript::variable("Ext.get('memoContainer".$this->oID."')"));

        // main/overview
        $minAge = preg_replace('/[^0-9]/', '', constant('_STORE_ACCOUNT_MIN_AGE'));
        $minAge = (int)$minAge;
        if(empty($minAge))
        {
            $minAge = 18;
        }
        $tpl_data = array('message' => $info->info_content, 'minAge' => $minAge);
        $tpl_data = array_merge($tpl_data, $this->order_data);
        ($plugin_code = $xtPlugin->PluginCode('order_edit.php:display_data')) ? eval($plugin_code) : false;

        $template = new Template();
        $template->_setTemplate('__xtAdmin');

        $tpl = 'order.html';
			
        require_once _SRV_WEBROOT.'/xtFramework/classes/class.order_edit.php';
        $orderEditBtnPanel = order_edit::hook_order_edit_display_tpl($this->oID, $js);
        $orderEditBtnPanel->setRenderTo(PhpExt_Javascript::variable("Ext.get('buttonContainer".$this->oID."')"));
			
        $extraButtonPanels = array();
        ($plugin_code = $xtPlugin->PluginCode('order_edit.php:display_tpl')) ? eval($plugin_code) : false;
        $extraButtonPanels = array_filter($extraButtonPanels);
        foreach($extraButtonPanels as $bp)
        {
            $bp->setRenderTo(PhpExt_Javascript::variable("Ext.get('buttonContainer".$this->oID."')"));
        }
			
        $page_data = $template->getTemplate('smarty', '/'._SRV_WEB_CORE.'pages/'.$tpl, $tpl_data);
        $page_data = preg_replace( "/\r|\n/", "", $page_data );
        $overviewPanel = new PhpExt_Panel();
        $overviewPanel->setAutoWidth(true)
            ->setHtml($page_data)
            ->setAutoLoad(false);
        $overviewPanel->setRenderTo(PhpExt_Javascript::variable("Ext.get('overviewContainer".$this->oID."')"));
        $top = (count($extraButtonPanels) + 1) * 35;
        $overviewPanel->setCssStyle("position:relative; top:{$top}px; margin-bottom:{$top}px");
			
        $js .= PhpExt_Ext::onReady(
					PhpExt_Javascript::stm("var orderdata = ".PhpExt_Javascript::jsonEncode($this->getOrderData()).""),
					PhpExt_Javascript::stm("var orderhistory = ".PhpExt_Javascript::jsonEncode($this->getOrderHistory()).""),
					//PhpExt_Javascript::stm("var downloads = ".PhpExt_Javascript::jsonEncode($this->getOrderDownloadsLog()).""),
			
                    $orderEditBtnPanel->getJavascript(false, 'orderEditButtonPanel'),
                    $overviewPanel->getJavascript(false, 'overviewPanel'),

					$productsPanel->getJavascript(false, "productsPanel"),
					PhpExt_Javascript::stm($productsTemplate->getJavascript(false,"productsTemplate")),
					$productsTemplate->overwrite(PhpExt_Javascript::variable("productsPanel.body"),PhpExt_Javascript::variable("orderdata")),
			
					$historyPanel->getJavascript(false, "historyPanel"),
					PhpExt_Javascript::stm($historyTemplate->getJavascript(false,"historyTemplate")),
					$historyTemplate->overwrite(PhpExt_Javascript::variable("historyPanel.body"),PhpExt_Javascript::variable("orderhistory")),

                    $layoutMemoPanel->getJavascript(false, "memoPanel")
			);


        if (!empty($this->orderData['order_download_log'])) {
            $js .= PhpExt_Ext::onReady(
                PhpExt_Javascript::stm("var downloads = ".PhpExt_Javascript::jsonEncode($this->getOrderDownloadsLog()).""),
                $downloadsJs = $downloadsPanel->getJavascript(false, 'downloadsPanel'),
                PhpExt_Javascript::stm($downloadsTemplate->getJavascript(false,"downloadsTemplate")),
                $downloadsTemplate->overwrite(PhpExt_Javascript::variable("downloadsPanel.body"),PhpExt_Javascript::variable("downloads"))
            );
        }
        foreach($extraButtonPanels as $k => $bp)
        {
            $js .= PhpExt_Ext::onReady($bp->getJavascript(false, 'extraButtonPanel'.$k));
		}

        ($plugin_code = $xtPlugin->PluginCode(__CLASS__.'_'.__FUNCTION__.':bottom')) ? eval($plugin_code) : false;

		return $this->getLayout().'<script type="text/javascript">' . $js .  '</script>';
	}

}

PhpExt_Javascript::sendContentType();
$admin_order_edit = new admin_order_edit($oID);
echo $admin_order_edit->displayJS ();
//__debug($admin_order_edit->orderData);
