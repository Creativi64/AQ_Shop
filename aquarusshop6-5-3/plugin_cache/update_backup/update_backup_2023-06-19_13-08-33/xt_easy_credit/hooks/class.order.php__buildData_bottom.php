<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

if($order_data['order_data']['payment_code'] == 'xt_easy_credit')
{
    global $db;

    $ser = $db->GetOne('SELECT rehash FROM '.TABLE_EASY_CREDIT_FINANCING.' WHERE orders_id=?', array($oID));
    if($ser)
    {
        $financing = unserialize($ser);
        $order_data['order_data']['easy_credit_financing'] = $financing;

        // order_info_option data in order.html
        $order_data['order_data']['order_info_options'][] = array('text'=>'<span class="bold" style="font-weight:bold">'.'ratenkauf by easycredit'.'</span>', 'value'=>'');
        $order_data['order_data']['order_info_options'][] = array('text'=>'fachl. Vorgangskennung', 'value'=>'<span class="bold" style="font-weight:bold">'.$financing['allgemeineVorgangsdaten']['fachlicheVorgangskennung'].'</span>');
        $order_data['order_data']['order_info_options'][] = array('text'=>'Vorgangskennung', 'value'=>$financing['allgemeineVorgangsdaten']['tbVorgangskennung']);
        $order_data['order_data']['order_info_options'][] = array('text'=>'Status', 'value'=>$financing['allgemeineVorgangsdaten']['status']);
        $order_data['order_data']['order_info_options'][] = array('text'=>'Entscheidung', 'value'=>$financing['entscheidung']['entscheidungsergebnis']);
        $order_data['order_data']['order_info_options'][] = array('text'=>'Gesamtsumme', 'value'=>$financing['ratenplan']['gesamtsumme']);
        $order_data['order_data']['order_info_options'][] = array('text'=>'Laufzeit', 'value'=>$financing['finanzierung']['laufzeit']);
    }
    else {
        error_log('xt_easy_credit build_order warning: Expected to find financing plan information but not found in db for order_id '.$oID);
    }
}