<?php

defined('_VALID_CALL') or die('Direct Access is not allowed.');

require_once _SRV_WEBROOT . _SRV_WEB_PLUGINS. 'xt_klarna_kp/classes/constants.php';

if (strstr($pid,'store_'))
{
    $tmp = explode('_',$parent);
    $store_id = (int)$tmp[1];

    $arr[]=array('text'=> __define('TEXT_KLARNA_CONFIG'),
        'url_e'     => '',
        'url_d'     => 'adminHandler.php?load_section=configuration&edit_id=101&store_id='.$store_id,
        'url_i'     => '',
        'url_h'     => 'https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/565936134',
        'tabtext'   => __define('TEXT_KLARNA_CONFIG'),
        'id'        => 'group_'.constant('KLARNA_ADMIN_CONFIG_GROUP').$store_id,
        'type'      => 'I',
        'leaf'      => '1',
        'icon'      => 'images/icons/../partner/klarna.png');
}

if (false && strstr($pid,'xt_klarna_kp_settlements'))
{
    global $db, $store_handler;
    $mids = array();
    foreach ($store_handler->getStores() as $store)
    {
        $key_mid = constant('_KLARNA_CONFIG_KP_MID');

        $test_mode = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store['id']. " WHERE config_key = '_KLARNA_CONFIG_KP_TESTMODE' ");
        if($test_mode)
        {
            $key_mid = '_KLARNA_CONFIG_KP_MID_TEST';
        }

        $mid = $db->GetOne("SELECT config_value FROM ".TABLE_CONFIGURATION_MULTI.$store['id']." WHERE config_key = '".$key_mid."' ");
        if(!array_key_exists($mid, $mids))
            $mids[$mid] = $store['id'];
    }
    foreach($mids as $mid => $store_id)
    {
        $s_mid = explode('_', $mid);
        $s_mid = $s_mid[0];
        $arr[]=array('text' => $s_mid.' Payouts',
            'url_e'=> '',
            'url_d'=>'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp_payouts&store_id='.$store_id,
            'url_i'=>'',
            'tabtext'=>'Payouts '.$s_mid,
            'id'=>'node_payouts_'.$s_mid,
            'type'=>'I',
            'leaf'=>'J',
            'icon'=> '');
        $arr[]=array('text'=> $s_mid.' Transactions ',
            'url_e'=> '',
            'url_d'=>'adminHandler.php?plugin=xt_klarna_kp&load_section=xt_klarna_kp_transactions&store_id='.$store_id,
            'url_i'=>'',
            'tabtext'=>'Transactions '.$s_mid,
            'id'=>'node_transactions_'.$s_mid,
            'type'=>'I',
            'leaf'=>'J',
            'icon'=> '');
    }
}
