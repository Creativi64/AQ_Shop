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

include '../xtFramework/admin/main.php';

global $is_pro_version;

if (!$xtc_acl->isLoggedIn()) {
	die('login required');
}

$navigation = new navigation();
$use_native_json_encode =false;

$node = $_POST['node'];
$node = str_replace('node_','',$node);
$param ='/[^a-zA-Z0-9_-]/';
$parent=preg_replace($param,'',$node);
$pid = $node;

/*
if (strstr($pid,'category') or strstr($pid,'subcat_')) {
	$arr = $navigation->getCatNavData($pid);
}
*/
$arr = false;

if (strstr($pid,'unasigned_cats') or strstr($pid,'subcat_')) {
	$store='';
	$arr = $navigation->getCatNavData($pid,$store,true);
}

if (strstr($pid,'cat_store') or strstr($pid,'subcat_') ) {
	$store='';
	if (strstr($pid,'cat_store')) {
		$ex = explode("cat_store",$pid);
		$store = $ex[1];
	}
	if (strstr($pid,'catst_')) {
		$ex = explode("catst_",$pid);
		$store = $ex[1];
	}
	$arr = $navigation->getCatNavData($pid,$store);
    $use_native_json_encode = true;
}

if (strstr($pid,'MediaGallery') or strstr($pid,'media_subcat_')) {
	$arr = $navigation->getMediaNavData($pid);
}

if (strstr($pid,'config_store')) {
	$arr = $store_handler->getStoresData($pid);
    $use_native_json_encode = true;
}

if (strstr($pid,'system_status')) {
	$arr = $navigation->getSysStatusNavData($pid);
}

if (strstr($pid,'store_')) {
	$conf = new configuration();
	$arr = $conf->getConfigNavData($pid);
}

if (strstr($pid,'configuration')) {
	$conf = new configuration();
	$arr = $conf->getConfigSysNavData($pid);	
}

//debugbreak();

if (strstr($pid,'veyton_partner')) {

    $partner = array();
    
    // default partner, werden immer angzeigt

   	$partner[]=array('name'=>'IT-Recht Kanzlei','url'=>'//www.it-recht-kanzlei.de/service/xt-commerce-agb.php','icon'=>'images/partner/it-recht-kanzlei.png');
	$partner[]=array('name'=>'magnalister','url'=>'//www.xtmulticonnect.com','icon'=>'images/partner/magnalister.png');
	//$partner[]=array('name'=>'Newsletter2Go','url'=>'//www.newsletter2go.de/de/newsletter/software/tool/code/g&huXkoP81DnUHSn','icon'=>'images/partner/nl2go.png');
	$partner[]=array('name'=>'Protected Shops','url'=>'//www.protectedshops.de/partner/xtcommerce','icon'=>'images/partner/protectedshops.png');
	$partner[]=array('name'=>'shipcloud','url'=>'//www.shipcloud.io/external_sites/xt_commerce_info','icon'=>'images/partner/shipcloud.png');
	
	// pro partner, werden nur in der pro angezeigt
	if($is_pro_version)
	{
        $partner[]=array('name'=>'Amazon Pay','url'=>'//www.xt-commerce.com/lp/amazon/','icon'=>'images/partner/amazon_pay.jpg');
        $partner[]=array('name'=>'easyCredit','url'=>'//www.easycredit-ratenkauf.de/xt-commerce-partner.htm','icon'=>'images/partner/easycredit.png');
        //$partner[]=array('name'=>'PayPal','url'=>'//addons.xt-commerce.com/partner/paypal/index.html','icon'=>'images/partner/paypal.png');
	    $partner[]=array('name'=>'Skrill','url'=>'//www.skrill.com/de/geschaeftlich/integration/','icon'=>'images/partner/skrill.png');
    }
	 
    $arr = array();
    foreach ($partner as $key => $val) {
        
       $arr[]=array('text'=>$val['name'],
                    'url_i'=>$val['url'],
                    'url_d'=>'',
                    'tabtext'=>$val['name'],
                    'id'=>'partner_'.$key,
                    'type'=>'I',
                    'leaf'=>'J',
                    'icon'=>$val['icon']); 
        
    }
    
}

($plugin_code = $xtPlugin->PluginCode('get_nodes.php:node')) ? eval($plugin_code) : false;

if(!is_array($arr))
    $arr = $navigation->readWestNavLevel($pid);

if (strstr($pid,'config_plugin'))
{
    $entries = array();
    $entries[]=array('name'=>'Premium Plugins','url' => '//xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/50790406','icon'=>'images/icons/plugin_go.png');

    foreach ($entries as $key => $val) {

        $arr[]=array('text'=>$val['name'],
            'url_e'=>$val['url'],
            'url_d'=>'',
            'url_i'=>'',
            'tabtext'=>'',
            'id'=>'',
            'type'=>'I',
            'leaf'=>'J',
            'icon'=>$val['icon']);
    }
}
else if (strstr($pid,'contentroot'))
{
    $entries = array();
    $entries[]=array('name'=>'Export Plugins','url' => '//xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/82051105','icon'=>'images/icons/plugin_go.png');

    foreach ($entries as $key => $val)
    {
        $inserted = array('text'=>$val['name'],
            'url_e'=>$val['url'],
            'url_d'=>'',
            'url_i'=>'',
            'tabtext'=>'',
            'id'=>'',
            'type'=>'I',
            'leaf'=>'J',
            'icon'=>$val['icon']);

        array_splice( $arr, 2, 0, array($inserted) );
    }
}

$s = $navigation->encodeMenuArray($arr, $use_native_json_encode);
header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
echo $s;
?>